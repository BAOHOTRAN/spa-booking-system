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

  // Sample services data
  const services = [
    {
      id: 1,
      name: 'Massage Therapy',
      price: 80,
      duration: 60,
      description: 'Relaxing full body massage',
      available: true
    },
    {
      id: 2,
      name: 'Facial Treatment',
      price: 60,
      duration: 45,
      description: 'Deep cleansing facial treatment',
      available: true
    },
    {
      id: 3,
      name: 'Body Wrap',
      price: 100,
      duration: 90,
      description: 'Detoxifying body wrap treatment',
      available: true
    },
    {
      id: 4,
      name: 'Hot Stone Massage',
      price: 120,
      duration: 75,
      description: 'Therapeutic hot stone massage',
      available: true
    },
    {
      id: 5,
      name: 'Aromatherapy',
      price: 90,
      duration: 60,
      description: 'Essential oils aromatherapy session',
      available: true
    }
  ]

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
      data: services,
      count: services.length,
      message: 'Services retrieved successfully'
    })
  }
}