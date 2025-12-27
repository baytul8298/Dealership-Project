import { Page, PageProps as InertiaPageProps } from '@inertiajs/core'
import { User } from './models/User'
import { Tenant } from './models/Tenant'

export interface PageProps extends InertiaPageProps {
  auth: {
    user: User | null
  }
  tenant: Tenant | null
  flash: {
    success?: string
    error?: string
    warning?: string
    info?: string
  }
  errors: Record<string, string>
}

declare module '@inertiajs/vue3' {
  export interface PageProps extends InertiaPageProps {
    auth: {
      user: User | null
    }
    tenant: Tenant | null
    flash: {
      success?: string
      error?: string
      warning?: string
      info?: string
    }
  }
}
