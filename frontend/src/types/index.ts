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
  password_confirmation: string
}

export interface Post {
  id: number
  body: string
  image_path: string
  created_at: string
  updated_at: string
  user: User,
  likes_count: number
  comments_count: number
  showComments?: boolean
  is_liked_by_user: boolean
  user_id_who_liked: number
}

export interface Comment {
  body: string
  created_at: string
  updated_at?: string
  user: User
  user_id: number
  post_id: number
  replies: Comment[]
  id: number
  parent: Comment
  parent_id: number
}
