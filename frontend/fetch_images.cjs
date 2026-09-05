const https = require('https');
https.get('https://unsplash.com/s/photos/diamond-ring', {
  headers: { 'User-Agent': 'Mozilla/5.0' }
}, (res) => {
  let data = '';
  res.on('data', chunk => data += chunk);
  res.on('end', () => {
    const regex = /"id":"([a-zA-Z0-9_-]{11})"/g;
    let match;
    const ids = new Set();
    while ((match = regex.exec(data)) !== null) {
      ids.add(match[1]);
    }
    console.log(Array.from(ids).slice(0, 10));
  });
});
