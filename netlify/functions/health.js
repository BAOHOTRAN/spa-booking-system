exports.handler = async (event, context) => {
  return {
    statusCode: 200,
    headers: {
      'Content-Type': 'application/json',
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Headers': 'Content-Type',
      'Access-Control-Allow-Methods': 'GET, POST, OPTIONS'
    },
    body: JSON.stringify({
      status: 'healthy',
      timestamp: Math.floor(Date.now() / 1000),
      date: new Date().toISOString(),
      version: '2.0.0',
      platform: 'Netlify Functions',
      developer: 'BAOHOTRAN',
      email: 'tqbao200468@gmail.com',
      message: 'SPA Booking System API is running on Netlify!'
    })
  }
}