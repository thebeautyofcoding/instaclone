export interface User {
  name: string
  avatar_url: string
  bio: string
  email: string
  id: number
}

export interface UserLoginData{
  email: string
  password: string
}

export interface UserRegisterData{
  name: string
  email: string
  password: string
  confirmPassword: string
}
