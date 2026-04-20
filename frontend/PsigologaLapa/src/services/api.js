// API Service for communicating with Laravel backend
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

console.log('API_BASE_URL:', API_BASE_URL)

function getAuthHeaders() {
  const authUser = JSON.parse(localStorage.getItem('authUser') || '{}')
  if (!authUser || !authUser.token) {
    return {}
  }

  return {
    Authorization: `Bearer ${authUser.token}`
  }
}

async function handleResponse(response) {
  let data = null
  try {
    data = await response.json()
  } catch {
    data = null
  }

  if (!response.ok) {
    const message = data?.message || data?.error || `HTTP ${response.status}`
    const error = new Error(message)
    error.response = { status: response.status, data }
    throw error
  }

  return data
}

const api = {
  async get(endpoint) {
    const url = `${API_BASE_URL}${endpoint}`
    console.log('GET request to:', url)
    try {
      const response = await fetch(url, {
        headers: {
          Accept: 'application/json',
          ...getAuthHeaders(),
        },
      })
      return await handleResponse(response)
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  },

  async post(endpoint, data) {
    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          ...getAuthHeaders(),
        },
        body: JSON.stringify(data),
      })
      return await handleResponse(response)
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  },

  async put(endpoint, data) {
    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        method: 'PUT',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          ...getAuthHeaders(),
        },
        body: JSON.stringify(data),
      })
      return await handleResponse(response)
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  },

  async delete(endpoint) {
    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        method: 'DELETE',
        headers: {
          Accept: 'application/json',
          ...getAuthHeaders(),
        },
      })
      return await handleResponse(response)
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  },
}

export default api
