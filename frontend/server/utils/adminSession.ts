import type { H3Event } from 'h3'

const ADMIN_COOKIE = 'llk_admin_token'

export function readAdminToken(event: H3Event): string | undefined {
  return getCookie(event, ADMIN_COOKIE)
}

export function setAdminToken(event: H3Event, token: string, remember: boolean): void {
  setCookie(event, ADMIN_COOKIE, token, {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    path: '/',
    maxAge: remember ? 60 * 60 * 24 * 14 : undefined,
  })
}

export function clearAdminToken(event: H3Event): void {
  deleteCookie(event, ADMIN_COOKIE, {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    path: '/',
  })
}
