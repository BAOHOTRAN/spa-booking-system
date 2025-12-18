// Simple in-memory storage (in production, use a real database)
let bookings = []

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

  const headers = {
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Allow-Methods': 'GET, POST, OPTIONS'
  }

  // GET - Retrieve all bookings
  if (event.httpMethod === 'GET') {
    return {
      statusCode: 200,
      headers,
      body: JSON.stringify({
        success: true,
        data: bookings,
        count: bookings.length,
        message: 'Bookings retrieved successfully'
      })
    }
  }

  // POST - Create new booking
  if (event.httpMethod === 'POST') {
    try {
      const data = JSON.parse(event.body)
      
      // Validate required fields
      if (!data.name || !data.email || !data.service_id) {
        return {
          statusCode: 400,
          headers,
          body: JSON.stringify({
            success: false,
            error: 'Missing required fields: name, email, service_id'
          })
        }
      }

      // Validate email format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailRegex.test(data.email)) {
        return {
          statusCode: 400,
          headers,
          body: JSON.stringify({
            success: false,
            error: 'Invalid email format'
          })
        }
      }

      // Create new booking
      const booking = {
        id: bookings.length + 1,
        customer_name: data.name,
        customer_email: data.email,
        service_id: parseInt(data.service_id),
        booking_date: data.date || new Date().toISOString().split('T')[0],
        booking_time: data.time || '10:00',
        status: 'pending',
        created_at: new Date().toISOString(),
        notes: data.notes || ''
      }

      bookings.push(booking)

      return {
        statusCode: 201,
        headers,
        body: JSON.stringify({
          success: true,
          data: booking,
          message: 'Booking created successfully'
        })
      }
    } catch (error) {
      return {
        statusCode: 400,
        headers,
        body: JSON.stringify({
          success: false,
          error: 'Invalid JSON data'
        })
      }
    }
  }

  // Method not allowed
  return {
    statusCode: 405,
    headers,
    body: JSON.stringify({
      success: false,
      error: 'Method not allowed'
    })
  }
}