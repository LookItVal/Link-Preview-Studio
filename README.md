# Link Preview Studio
Link Preview Studio is a technical utility designed for marketing and social media teams to verify Open Graph and Meta tag integrity before hitting "send."

This project was built as a response to the AGENCYOF Developer Code Challenge.

## The Stack
I chose a decoupled, modern architecture to demonstrate versatility across different ecosystems:

Frontend: Nuxt.js (Vue 3, TypeScript, Tailwind CSS)

Animations: GSAP for fluid micro-interactions.

Backend: Laravel (Headless API)

Local Dev: Laravel Sail (Docker-based)

Deployment: Cloudflare Pages (Frontend) + Cloudflare Tunnels + Home Server (Backend)

## Quick Start

### Live Demo: [here](https://link-preview-studio.lookitval.com)

### Local Setup

#### Requirements

- Git (for cloning the repo)
- Docker (for Laravel Sail)
- Node.js (for Nuxt.js)

#### Setup
1. Clone the repo:
```bash
git clone https://github.com/lookitval/link-preview-studio.git
cd link-preview-studio
```

2. Start the backend with Laravel Sail:
```bash
cd backend
./vendor/bin/sail up -d
```

3. Start the frontend:
```bash
cd ../frontend
npm install
npm run dev
```

## Architecture & Decisions

### Frontend

#### Setup
The frontend is built with Nuxt.js. It is set up with an incredibly minimal structure to keep things simple and focused on the core functionality.
Each section of the UI is broken down into components, and the client side logic is organized into composables for clean separation of concerns.
All items related to the frontend are in the `frontend/app` directory which looks like this:
```
app/
├── assets/
│   └── main.css
├── components/
│   └── preview/
│       ├── card/
│       │   ├── Facebook.vue
│       │   ├── LinkedIn.vue
│       │   ├── Slack.vue
│       │   └── Twitter.vue
│       ├── History.vue
│       └── UrlSearchbar.vue
├── composables/
│   ├── useHistory.ts
│   └── useMetaSearch.ts
├── pages/
│   └── index.vue
└── app.vue
```

#### Why Nuxt.js?
Because I like it. Nuxt is a powerful framework that allows for rapid development with Vue 3, and I find its organization elegant and clean.

### Backend

#### Setup
The backend is a simple Laravel API that only has a single endpont at `/api/metadata` to fetch and parse meta tags from a given URL.
The backend is entirely headless and open, and does not store any data from the requests it processes.
It uses only a single controller at `app/Http/Controllers/MetadataController.php`, a single request handler at `app/Http/Requests/MetadataRequest.php`, and a single test file at `tests/Feature/MetadataEndpointTest.php`.

#### Why Laravel?
I have never built a Laravel app before, and I have very little experience with PHP. This was a great opportunity to prove that I can jump into this ecosystem and build something functional.

#### Why Cloudflare Tunnels?
I could have deployed this into a traditional cloud provider, but I have a home server that I use for personal projects that already is exposed to a cloudflare tunnel. This was a quick and cost-effective way to get the backend online without needing to set up a whole new hosting environment.

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
- [ ] Smooth transitions and micro-interactions in the UI
- [x] Dark mode & light mode switch
  - [x] 3 way toggle for dark/light/auto (system preference)
- [ ] Mobile-responsive layout
- [ ] A UTM parameter builder that appends params to the URL before fetching
- [x] Live deployment

## Live Deployment & GitOps
This application is deployed via a GitOps pipeline:

CI: GitHub Actions builds a Docker image and pushes it to GHCR.

CD: A k3s cluster pulls the updated image automatically upon merge.

Tunnel: The backend is exposed securely via a Cloudflare Tunnel, while the Nuxt frontend is hosted on Cloudflare Pages for edge-network performance.

Live URL: [here](https://link-preview-studio.lookitval.com)

## Testing Urls

The following urls were used to test different scenarios and edge cases for the link preview functionality:
- https://example.com
- https://twitter.com
- https://www.usatoday.com/story/entertainment/tv/2026/04/13/harry-potter-ralph-fiennes-voldemort-casting-hbo-tilda-swinton/89587435007/?utm_source=firefox-newtab-en-us
- https://slack.com
- http://facebook.com
- http://linkedin.com
- https://www.psypost.org/toddlers-are-happier-giving-treats-to-others-than-receiving-them-study-finds/
- https://deepmind.google/models/gemma/gemma-4/
- https://huggingface.co/collections/google/gemma-4
- https://en.wikipedia.org/wiki/Standard_Model



## Future Improvements