import { EventContext } from '@cloudflare/workers-types';

interface Env {
  CLOUDFLARE_TUNNEL_ID: string;
}

export async function onRequest(context: EventContext<Env, 'path', {}>) {
  const url = new URL(context.request.url);
  
  // Cleanly parse catch-all route parameters with a fallback
  const pathSegments = context.params.path;
  const remainingPath = Array.isArray(pathSegments) ? pathSegments.join('/') : pathSegments || '';
  
  console.log(`[Proxy] Incoming request intercepted for path: /api/${remainingPath}`);

  const tunnelId = context.env.CLOUDFLARE_TUNNEL_ID; 
  if (!tunnelId) {
    console.error("[Proxy CRITICAL] CLOUDFLARE_TUNNEL_ID environment variable is missing in dashboard settings!");
    return new Response("Internal Server Configuration Error", { status: 500 });
  }

  // Construct the internal virtual network address
  const backendUrl = `https://${tunnelId}.cfargotunnel.com/${remainingPath}${url.search}`;
  console.log(`[Proxy] Routing internally to: ${backendUrl}`);

  try {
    // Clone incoming request to preserve Method (POST/PUT), Headers, and Body streams
    const newRequest = new Request(backendUrl, context.request);
    
    // Explicitly set the host header so your cluster's cloudflared knows where to route this
    newRequest.headers.set("Host", "link-preview-studio-api.domain.com");
    
    const response = await fetch(newRequest);
    
    console.log(`[Proxy] Backend responded with HTTP Status: ${response.status}`);
    return response;

  } catch (error: any) {
    // Captures network dropouts, SSL handshake issues, or faulty tunnel IDs
    console.error(`[Proxy CRITICAL] Fetch pipeline failed completely: ${error.message || error}`);
    
    return new Response(
      JSON.stringify({ 
        error: "Bad Gateway", 
        message: "Failed to communicate with internal cluster backend tunnel." 
      }), 
      { 
        status: 502,
        headers: { "Content-Type": "application/json" }
      }
    );
  }
}