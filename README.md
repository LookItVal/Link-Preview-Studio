# Link Preview Studio

A decoupled application designed to extract, analyze, and visualize structured web metadata (Open Graph, Twitter Cards, Schema.org/JSON-LD).
Marketing teams frequently share URLs across platforms like Slack, Twitter/X, and LinkedIn, often without knowing how the destination platform's crawler will interpret their site's metadata. This tool provides an emulation of those platform-specific scrapers.

## Quick Start

### Live Deployment: [Link Preview Studio Live](https://link-preview-studio.lookitval.com)

### Local Development

This project is structured as a monorepo containing a disconnected frontend in *Nuxt.js* and a headless backend in *Laravel*.

#### Prerequisites

- Git (for cloning the repo)

- Docker Desktop (for Laravel Sail)
- PHP & Composer (for initial dependency installation)
- Node.js 18+ & npm/pnpm/yarn (for Nuxt.js)

#### Installation

1. Clone the Repository
```bash
git clone https://github.com/your-username/link-preview-studio.git
cd link-preview-studio
```

2. Start the Backend (Laravel Sail)
```bash
cd backend

# Load in basic environment variables
cp .env.example .env

# Install dependencies to pull in the Sail binary
composer install

# Start the Docker containers in the background
./vendor/bin/sail up -d

# Back Home
cd ..
```

3. Start the Frontend (Nuxt.js)
```bash
cd frontend

# Ensure the API base points to your local Sail instance
echo "NUXT_PUBLIC_API_BASE=http://localhost/api" > .env

# Install dependencies
npm install

# Start the development server
npm run dev
```

## Deployment & DevOps Architecture

```mermaid
graph TD
    A[Local Code] -- Push to Main --> B[GitHub Actions]
    
    subgraph CI_CD_Workflows [CI/CD Workflows]
        B --> C[Nuxt Build]
        B --> D[Laravel Build]
        C -- Deploy to GH Pages --> E[GitHub Pages]
        D -- Run Pest Tests --> F[Build Docker Image]
        F -- Push to GHCR --> G[GitHub Container Registry]
    end
    
    subgraph Client_Environment [Client Side]
        CL[User Browser]
    end

    subgraph Production_Cluster [K3s Cluster]
        G -- Image Pull --> H[Deployment Controller]
        H --> P[Backend Pods]
        P -- Internal Port 8000 --> S[ClusterIP Service]
        J[Cloudflare Tunnel] -- Secure Ingress --> S
    end

    E -- 1. Load Web App --> CL
    CL <-- 2. Query Metadata API --> J
```

### Infrastructure Logic & Flow

The architecture represents a strictly decoupled Static Front-end / Containerized Back-end pattern:

1. **Frontend Delivery**: The Nuxt.js application is built as a static site and served directly via GitHub Pages. This keeps deployment simple and ensures global availability of the UI assets to the user's browser.
2. **API Communication**: Once the browser loads the app, it initiates an fetch request to the backend. This request is routed through a Cloudflare Tunnel, which acts as a secure gateway to the private K3s cluster.
3. **Backend Processing**: The K3s cluster receives the request via a ClusterIP Service, which load-balances the traffic across multiple instances of the Backend Pods.

### GitHub Actions & CI/CD Logic

The automation logic resides in the root directory under .github/workflows/. These actions manage the lifecycle of both applications independently:

- **Frontend Workflow** `.github/workflows/frontend.yml`:
  - Triggered by changes in the frontend/ directory.
  - Installs dependencies, runs the Nuxt build, and utilizes the actions/deploy-pages action to push the build artifacts to the GitHub Pages environment.

- **Backend Workflow** `.github/workflows/backend.yml`:
  - Triggered by changes in the backend/ directory.
  - **Validation**: Executes php artisan test within a PHP environment before proceeding to the build phase. This ensures that no broken extraction logic reaches the container registry.
  - **Containerization**: Builds a production Docker image based on FrankenPHP.
  - **Registry**: Authenticates with GHCR and pushes the tagged image. The K3s cluster is configured with a node-agent that detects the new image and performs a rolling update of the Deployment.

### Self-Hosted K3s Setup

The backend is hosted on a local server managed by K3s (a lightweight Kubernetes distribution).

- **Internal Routing**: Traffic from the public internet never reaches the server directly. Instead, a Cloudflare Tunnel (cloudflared) creates a secure, encrypted outbound connection from within the local network to Cloudflare's edge.
- **Service Exposure**: The tunnel routes requests directly to a Kubernetes ClusterIP Service, which serves as the entry point for the backend pods running FrankenPHP/Octane. This setup allows the backend to remain entirely private (no open ports) while being globally accessible.

## Architectural Analysis

### Backend (Laravel Headless API)

#### 1. Minimalist API Interface

This API returns the full structured payload of the target URL. This allows the client-side "Studio" to visualize exactly how different platforms (Facebook, Twitter, Slack) would prioritize the data.

**Production Endpoint**: `https://link-preview-studio-api.lookitval.com/api/metadata`
- Method: `POST` / `GET`.
- Query Parameter: `q=[encoded_url]`

**Response Schema**:
```json
{
  "status": "success",
  "data": {
    "title": "Page Title",
    "description": "Meta description...",
    "meta": { "viewport": "...", "theme-color": "..." },
    "og": { "title": "...", "image": "...", "type": "article" },
    "twitter": { "card": "summary_large_image", "site": "@user" },
    "jsonLd": [ ... ],
    "icons": [ ... ],
    "fallback": {
      "image": "/path/to/extracted/img.png",
      "paragraph": "First significant text block used if meta is missing..."
    }
  }
}
```

#### 2. Data Flow

```mermaid
sequenceDiagram
    autonumber
    participant U as User (Frontend)
    participant B as Laravel API
    participant T as Target Website
    
    U->>B: POST /api/metadata {url}
    Note over B: SSRF & DNS Validation
    B->>T: HTTP GET (Spoofing User-Agent)
    T-->>B: 200 OK (HTML Source)
    
    rect rgb(40, 44, 52)
        Note over B: MetadataExtractor Waterfall
        Note right of B: 1. Extract standard meta tags (title, description)
        Note right of B: 2. Extract og: & twitter: tags
        Note right of B: 3. Parse JSON-LD (Schema.org)
        Note right of B: 4. Extract remaining meta tags 
        Note right of B: 5. Heuristic DOM Query (p tags, icons)
    end
    
    B-->>U: JSON {status: success, data: {...}}
```


## Frontend (Nuxt.js)

### Client-Side Logic

The frontend's primary responsibility is to interpret the verbose JSON payload from the API and map it to specific platform requirements. Since each social platform (Slack, LinkedIn, etc.) has its own unique "priority waterfall" for which tags it displays first, this allows each component to handle the tag mapping dynamically.

### Persistence Strategy: Why `localStorage`?

For session persistence and memory, everything is stored on the frontend using `localStorage`.
This approach ensures that everything is saved for the user without adding any complexity to the backend, and allowing the application to quickly restore the user's session state upon revisiting the site.
This does require a cookie notification to inform users that their session data is being stored locally.

## Requirements Checklist
(This is mostly for me to check off the boxes as I go)

### Must Haves
- [x] URL input field
- [x] Server-side URL fetch
- [x] Meta tag extraction
- [x] At least two platform preview cards
  - [x] Twitter
  - [x] Slack
  - [x] LinkedIn
  - [x] Facebook
- [x] Checked URL history
- [x] Error handling
  - [x] Invalid URL format
  - [x] Timeouts
  - [x] Pages with zero meta tags
  - [x] Non-200 HTTP responses
- [x] Cookie consent modal
- [x] README.md

### Nice to Haves
- [x] Additional platform preview card(s)
- [ ] A meta tag completeness checklist or "health score"
- [ ] Ability to copy any individual meta tag value to clipboard
- [x] Smooth transitions and micro-interactions in the UI
- [x] Dark mode & light mode switch
  - [x] 3 way toggle for dark/light/auto (system preference)
- [x] Mobile-responsive layout
- [ ] A UTM parameter builder that appends params to the URL before fetching
- [x] Live deployment

## Future Considerations

The next step is to implement more specialized scrapers. Right now this generic scraper works for most sites, but platforms like Slack clearly use specialized techniques to pull richer information from big domains.

### 1. The Wikipedia / Content Driver

Wikipedia is a great example. Slack seems to pull significantly information from the actual body of the page when no descriptions are present.
I want to build a driver that targets the Lead Section: Specifically pulls the first 2-3 paragraphs from the Wikipedia content div rather than just the meta tags.

### 2. High-Fidelity Video Embedding (YouTube/Vimeo)

When you paste a YouTube link in Slack it embeds an actual video player. To emulate this, the scraper needs to pull specific data:
- **oEmbed Discovery**: Most professional platforms use the oEmbed spec (discovered via `<link rel="alternate" type="application/json+oembed">`). This provides the exact iframe HTML, dimensions, and provider information needed for an inline embed.
- **YouTube Specifics**: Pulling `og:video:url` and `og:video:secure_url` specifically allows Slack to "unfurl" the video into a native-feeling player without the user leaving the chat. I'll be adding a "Playable Preview" mode to my Studio to emulate this behavior.

### 3. Specialized Page Summaries

Beyond video and wiki content, several other page types deserve custom rendering logic and should be looked into:
- **E-commerce (Amazon/Shopify)**: Scrapers might pull data from price or reviews in some previews.
- **Professional Profiles (LinkedIn)**: Scrapers might pull specific data such as company information or job posting details (like location and salary range) from JSON-LD schemas.
- **Documentation (GitHub/MDN)**: Scrapers might pull repo statistics (stars, forks) or documentation breadcrumbs.

### 4. More Previews

Some Previews like LinkedIn seem to have different versions for mobile and desktop, and I think I only made the preview for mobile. More research should go into testing these links from the actual platform to know how they were done accurately.
Slack also appears to be slightly different in the live enviroment than the example previews, in particular in that it has an arrow to collapse or expand parts of the preveiw, and bit of text saying how big the image(?) is as well. This was not taken into account in this app.
In this app I used mostly `https://www.opengraph.xyz/` as my reference for how previews should look and I have come to find that their previews are not always consistent with what some platforms actually display, or what other preview tools show.
Additionally tools like `https://metatags.io/` also include previews for things like google, and while I know the exact description on google will actually vary depending on the search context, I find this a useful addition.
Below are some examples of previews I think would be handy:
- Google
- Pinterest
- WhatsApp
- Discord

### 5. Fetching Limitations

When looking at some sources, it seems that some source urls could not be fetched, some of which could not be fetched by other scrapers, and some of them could. Each of these should be looked into to understand the limitations and improve the robustness of the scraper.
Additionally, different scrapers seemed to pull different sets of meta tags or page content depending on the source.
Each of these were checked against the following sources:
- `metatags.io/`
- `www.opengraph.xyz/`
- Live Slack (just sending a message with the URL)
- `www.linkedin.com/post-inspector/inspect/` <- Native inspector for LinkedIn
- A native post inspector does seem to exist for both facebook and twitter but they both require accounts to access. I do not have a facebook or twitter account anddid not explore these further.

Below are examples of discrepancies in how different scrapers fetched URLs and their meta tags. (Ignore the sources, most of these are found from random news feeds or social media posts at random.)

  1. `https://www.cbsnews.com/news/pete-hegseth-impeachment-articles-house-democrats/?utm_source=firefox-newtab-en-us`
     - This app, `metatags.io/`, and `www.opengraph.xyz/` all fetched this URL with similar styles.
     - Live Slack and Linkedin Post Inspector both were unable to fetch the leading image.
     - `metatags.io/` was unable to fetch the favicon.
  2. `https://www.usatoday.com/story/entertainment/movies/2026/04/16/spaceballs-sequel-cinemacon/89638149007/?utm_source=firefox-newtab-en-us`
     - Everything fetched this Url about the same
     - `metatags.io/` was unable to fetch the favicon.
  3. `https://www.popularmechanics.com/science/archaeology/a71016439/cold-war-bunker-castle/?utm_source=firefox-newtab-en-us`
     - `metatags.io/` and Slack were both able to fetch the favicon but this app was not able to.
  4. `https://www.npr.org/2026/04/15/nx-s1-5784021/eric-swalwell-resignation-california-congress?utm_source=firefox-newtab-en-us`
     - This app spends a long time processing this request before returning "NetworkError when attempting to fetch resource."
     - This seems to happen all across npr.org URLs.
     - All other preview systems were able to fetch this URL without issues.
  5. `https://www.si.com/nba/chris-paul-clippers-lose-meme-warriors?utm_source=firefox-newtab-en-us`
     - `https://metatags.io/` and Slack were both able to fetch the favicon but this app was not able to.
  6. `https://youtu.be/oaXRREHVkHo?si=Y93gy5e6DNqWF_pv`
     - Almost none of the preview systems were able to fetch this URL correctly.
     - `metatags.io/` Pulled generic Titles and descriptions for all of youtube rather than about the video, and no image at all.
     - `www.opengraph.xyz/` Errored out: `This website is rate-limiting requests. To allow our scanner through, add these IP ranges to your firewall: 74.220.48.0/24 and 74.220.56.0/24.`
     - The official LinkedIn Post inspector was able to pull the title of the video, but no image.
     - Slack of course pulled not only the title and image, but also an iframe embedding the video.
     - This app pulled the thumbnail of the video, the title of the video, and also the description of the video itself from the creator.
  7. `https://www.thedailybeast.com/trump-yanks-millions-from-catholic-charities-amid-pope-feud/`
    - This app is unable to fetch the favicon but all of the other apps are.
  8. `https://en.wikipedia.org/wiki/Standard_Model`
     - Slack was able to pull a large portion of the article in where the description was
     - No other preview system pulled any description at all
     - Slack listed the title of the host as "Wikipedia"
     - This app listed the title of the host as "Wikimedia Foundation, Inc."
     - `metatags.io/` was unable to fetch the host name and instead listed the full url  "https://en.wikipedia.org/wiki/Standard_Model"
  9. `https://www.self.com/story/incline-walking-vs-running?utm_source=firefox-newtab-en-us`
     - On this app, all image previews work except for in Slack (they should be referencing the same url so I'm not sure how this came to be)
     - On this app, the title "Incline Walking vs. Running: What’s the Better Workout?" is converted into "Incline Walking vs. Running: Whatâs the Better Workout?". The `’` character is rendering as a `â` character instead.
     - `metatags.io/` was unable to fetch the image in Most previews, but for some reason the Pinterest preview was able to.
     - All other preview systems were able to fetch the image without issues.
  10. `https://www.linkedin.com/in/qcecil/`
      - This app encountered an error: `NetworkError when attempting to fetch resource.` This error happened nearly instantly.
      - `metatags.io/` silently errored out and did not provide any preview.
      - `www.opengraph.xyz` gave the following error: `The website's server encountered an error. Try again later.`
      - The linkedin post inspector was able to pull the title of the profile, but no image.
      - Slack was able to pull the title and the following description:
        - Experience: Propaganda3 · Education: Johnson County Community College · Location: Kansas City Metropolitan Area · 18 connections on LinkedIn. View Quinn Cecil’s profile on LinkedIn, a professional community of 1 billion members.
  11. `https://www.reddit.com/user/LookItVal/`
      - This app pulled the title: "Reddit - Please wait for verification" with no description.
      - `metatags.io/` silently errored out and did not provide any preview.
      - `www.opengraph.xyz` Pulled the same information as this app.
      - The linkedin post inspector was able to pull the title "Check out LookItVal’s Reddit profile" and a relevant image.
      - Slack was able to pull the title and a relevant description, and a relevant image.
  12. `https://www.google.com/`
      - This app pulled the title and description with no image.
      - Slack actaully did not provide a preview for this URL at all.
      - `metatags.io/` Seems to have only pulled the title, no description and no image.
  

In testing at home I remember having other outright errors pop up, but I did not document them at the time and I am having a hard time finding URLs that cause more outright errors.


### 6 Minor Issues

- I dont think the color theme is saved at all. Should probably save the last set color theme to a cookie so it doesnt reset on reload.
- If you rush clicking around while animations are happening, they can sometimes get stuck or behave unexpectedly. This should be solvable by disabling interactions during animations or properly handling animation states.