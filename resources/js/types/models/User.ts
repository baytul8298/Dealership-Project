export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  tenant_id: number | null
  created_at: string
  updated_at: string
  roles?: Role[]
  permissions?: Permission[]
}

export interface Role {
  id: number
  name: string
  display_name: string
  description: string | null
  permissions?: Permission[]
}

export interface Permission {
  id: number
  name: string
  display_name: string
  description: string | null
}
