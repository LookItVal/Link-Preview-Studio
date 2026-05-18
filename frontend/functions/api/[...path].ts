import { EventContext } from '@cloudflare/workers-types';

// Define your expected environment variables
interface Env {
  CLOUDFLARE_TUNNEL_ID: string;
}

export async function onRequest(context: EventContext<Env, 'path', {}>) {
  const url = new URL(context.request.url);
  const remainingPath = (context.params.path as string[]).join('/');
  
  // Pull the ID securely from the Cloudflare environment context
  const tunnelId = context.env.CLOUDFLARE_TUNNEL_ID; 
  const backendUrl = `https://${tunnelId}.cfargotunnel.com/${remainingPath}${url.search}`;
  
  const newRequest = new Request(backendUrl, context.request);
  newRequest.headers.set("Host", "link-preview-studio-api.domain.com");
  
  return fetch(newRequest);
}