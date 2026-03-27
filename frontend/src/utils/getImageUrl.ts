export function getOptimizedImageUrl(url: string, width: number = 400): string {
  if (!url) return '';

  if (url.includes('store.storeimages.cdn-apple.com')) {
    const height = Math.round(width * 1.1);
    return url
      .replace(/wid=\d+/, `wid=${width}`)
      .replace(/hei=\d+/, `hei=${height}`);
  }

  if (url.includes('cdn.shopify.com')) {
    if (url.match(/_(\d+)x(\d+)\./)) {
      return url.replace(/_(\d+)x(\d+)\./, `_${width}x${width}.`);
    }
    return url;
  }

  if (url.includes('images-na.ssl-images-amazon.com')) {
    return url.replace(/_SL\d+_\./, `_SL${width}_.`);
  }

  if (url.includes('images.canadagoose.com')) {
    if (url.includes('w_')) {
      return url.replace(/w_\d+/, `w_${width}`);
    }
    return url.replace(/,c_scale/, `,w_${width},c_scale`);
  }

  return url;
}
