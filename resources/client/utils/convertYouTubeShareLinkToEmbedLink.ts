// https://youtu.be/180_xij5Kgg?si=0ZyjCOXnpDXyc-EN
//https://www.youtube-nocookie.com/embed/180_xij5Kgg?si=SwWfPrmtkTgGFCln

export function convertYouTubeShareLinkToEmbedLink(link: string): string | null {
  const shareUrlPattern = /^https:\/\/youtu\.be\/(.+)$/;

  const match = link.match(shareUrlPattern);
  if (match && match[1]) {
    const videoId = match[1];
    return `https://www.youtube-nocookie.com/embed/${videoId}`;
  }

  return null;
}
