const BASE = '/api/v1'

function getToken() {
  return localStorage.getItem('token')
}

async function request(method, path, body = null, auth = true) {
  const headers = { 'Content-Type': 'application/json' }

  if (auth) {
    const token = getToken()
    if (token) headers['Authorization'] = `Bearer ${token}`
  }

  let res
  try {
    res = await fetch(BASE + path, {
      method,
      headers,
      body: body ? JSON.stringify(body) : null,
    })
  } catch {
    throw new Error('Could not connect to the server. Is Docker running on port 8080?')
  }

  let data
  try {
    data = await res.json()
  } catch {
    throw new Error('Server returned an invalid response')
  }

  if (data.status === 'error') {
    throw new Error(data.message)
  }

  return data.data
}

export const login = (email, password) =>
  request('POST', '/auth/login', { email, password }, false)

export const register = (name, email, password, age) =>
  request('POST', '/auth/register', { name, email, password, age }, false)

export const logout = () => request('POST', '/auth/logout')

export const getMe = (id) => request('GET', `/users/${id}`)
export const updateUser = (id, data) => request('PUT', `/users/${id}`, data)

export const getUserWishlists = (userId) => request('GET', `/users/${userId}/wishlists`)
export const getWishlist = (id) => request('GET', `/wishlists/${id}`)
export const createWishlist = (data) => request('POST', '/wishlists', data)
export const updateWishlist = (id, data) => request('PUT', `/wishlists/${id}`, data)
export const deleteWishlist = (id) => request('DELETE', `/wishlists/${id}`)

export const getItems = (wishlistId) => request('GET', `/wishlists/${wishlistId}/items`)
export const addItem = (wishlistId, data) => request('POST', `/wishlists/${wishlistId}/items`, data)
export const updateItem = (wishlistId, itemId, data) => request('PUT', `/wishlists/${wishlistId}/items/${itemId}`, data)
export const deleteItem = (wishlistId, itemId) => request('DELETE', `/wishlists/${wishlistId}/items/${itemId}`)
export const reserveItem = (wishlistId, itemId) => request('POST', `/wishlists/${wishlistId}/items/${itemId}/reserve`)
export const unreserveItem = (wishlistId, itemId) => request('DELETE', `/wishlists/${wishlistId}/items/${itemId}/reserve`)

export const getFriend = (id) => request('GET', `/users/${id}`)
export const getFriends = (userId) => request('GET', `/users/${userId}/friends`)
export const addFriend = (userId, data) => request('POST', `/users/${userId}/friends`, data)
export const removeFriend = (userId, friendId) => request('DELETE', `/users/${userId}/friends/${friendId}`)
export const createInvite = (userId) => request('POST', `/users/${userId}/invites`)
export const useInvite = (userId, code) => request('POST', `/users/${userId}/invites/use`, { code })
