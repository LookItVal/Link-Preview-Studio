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
The backend is a simple Laravel API that only has a single endpont to fetch and parse meta tags from a given URL. It uses Guzzle for HTTP requests and a custom parser to extract the relevant meta tags.

#### Why Laravel?
I have never built a Laravel app before, and I have very little experience with PHP. This was a great opportunity to prove that I can jump into this ecosystem and build something functional.

#### Why Cloudflare Tunnels?
I could have deployed this into a traditional cloud provider, but I have a home server that I use for personal projects that already is exposed to a cloudflare tunnel. This was a quick and cost-effective way to get the backend online without needing to set up a whole new hosting environment.

## Requirements Checklist
(This is mostly for me to check off the boxes as I go)

### Must Haves
- [ ] URL input field
- [ ] Server-side URL fetch
- [ ] Meta tag extraction
- [ ] At least two platform preview cards
  - [ ] Twitter
  - [ ] Slack
  - [ ] LinkedIn
  - [ ] Facebook
- [ ] Checked URL history
- [ ] Error handling
  - [ ] Invalid URL format
  - [ ] Timeouts
  - [ ] Pages with zero meta tags
  - [ ] Non-200 HTTP responses
- [x] README.md

### Nice to Haves
- [ ] Additional platform preview card(s)
- [ ] A meta tag completeness checklist or "health score"
- [ ] Ability to copy any individual meta tag value to clipboard
- [ ] Smooth transitions and micro-interactions in the UI
- [ ] Dark mode & light mode switch
  - [ ] 3 way toggle for dark/light/auto (system preference)
- [ ] Mobile-responsive layout
- [ ] A UTM parameter builder that appends params to the URL before fetching
- [ ] Live deployment

## Live Deployment & GitOps
This application is deployed via a GitOps pipeline:

CI: GitHub Actions builds a Docker image and pushes it to GHCR.

CD: A k3s cluster pulls the updated image automatically upon merge.

Tunnel: The backend is exposed securely via a Cloudflare Tunnel, while the Nuxt frontend is hosted on Cloudflare Pages for edge-network performance.

Live URL: [Insert your tunnel/pages link here]

## Future Improvements