exports.handler = async (event, context) => {
  // Handle CORS preflight
  if (event.httpMethod === 'OPTIONS') {
    return {
      statusCode: 200,
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Access-Control-Allow-Methods': 'GET, POST, OPTIONS'
      }
    }
  }

  // Sample services for stats calculation
  const services = [
    { id: 1, name: 'Massage Therapy', price: 80 },
    { id: 2, name: 'Facial Treatment', price: 60 },
    { id: 3, name: 'Body Wrap', price: 100 },
    { id: 4, name: 'Hot Stone Massage', price: 120 },
    { id: 5, name: 'Aromatherapy', price: 90 }
  ]

  // Calculate stats
  const totalServices = services.length
  const prices = services.map(s => s.price)
  const averagePrice = prices.reduce((a, b) => a + b, 0) / prices.length
  const mostExpensive = Math.max(...prices)
  const cheapest = Math.min(...prices)

  return {
    statusCode: 200,
    headers: {
      'Content-Type': 'application/json',
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Headers': 'Content-Type',
      'Access-Control-Allow-Methods': 'GET, POST, OPTIONS'
    },
    body: JSON.stringify({
      success: true,
      data: {
        total_services: totalServices,
        total_bookings: 0, // Will be dynamic when connected to real database
        average_price: Math.round(averagePrice * 100) / 100,
        most_expensive_service: mostExpensive,
        cheapest_service: cheapest,
        revenue_potential: prices.reduce((a, b) => a + b, 0),
        platform: 'Netlify Functions',
        last_updated: new Date().toISOString()
      },
      message: 'Statistics calculated successfully'
    })
  }
}