type RouteParameters = {
login: never,
'login.store': never,
logout: never,
'password.request': never,
'password.email': never,
'password.reset': {
token: string | number,
},
'password.update': never,
register: never,
'register.store': never,
'verification.notice': never,
'verification.verify': {
id: string | number,
hash: string | number,
},
'verification.send': never,
'password.confirm': never,
'password.confirm.store': never,
'password.confirmation': never,
'two-factor.login': never,
'two-factor.login.store': never,
'two-factor.enable': never,
'two-factor.disable': never,
'two-factor.confirm': never,
'two-factor.qr-code': never,
'two-factor.secret-key': never,
'two-factor.recovery-codes': never,
'two-factor.regenerate-recovery-codes': never,
'examples.paginator.users': never,
'examples.table.users': never,
settings: never,
'profile.edit': never,
'profile.update': never,
'profile.destroy': never,
'settings.password.edit': never,
'settings.password.update': never,
'two-factor.show': never,
'boost.browser-logs': never,
index: never,
dashboard: never,
'appearance.edit': never,
'storage.local.upload': {
path: string | number,
},
};
export function route<T extends keyof RouteParameters>(name: T, parameters?: [RouteParameters[T]] extends [never] ? Record<string, never> : RouteParameters[T], absolute: boolean = false): string {
    let url: string = '/' + routes[name]

    if (parameters) {
        for (const [key, value] of Object.entries(parameters)) {
            url = url.replace(`{${key}}`, String(value))
        }
    }

    if (absolute) {
        url = window.location.origin + url
    }

    return url
}
const routes = {
    "login": "login",
    "login.store": "login",
    "logout": "logout",
    "password.request": "forgot-password",
    "password.email": "forgot-password",
    "password.reset": "reset-password/{token}",
    "password.update": "reset-password",
    "register": "register",
    "register.store": "register",
    "verification.notice": "email/verify",
    "verification.verify": "email/verify/{id}/{hash}",
    "verification.send": "email/verification-notification",
    "password.confirm": "user/confirm-password",
    "password.confirm.store": "user/confirm-password",
    "password.confirmation": "user/confirmed-password-status",
    "two-factor.login": "two-factor-challenge",
    "two-factor.login.store": "two-factor-challenge",
    "two-factor.enable": "user/two-factor-authentication",
    "two-factor.disable": "user/two-factor-authentication",
    "two-factor.confirm": "user/confirmed-two-factor-authentication",
    "two-factor.qr-code": "user/two-factor-qr-code",
    "two-factor.secret-key": "user/two-factor-secret-key",
    "two-factor.recovery-codes": "user/two-factor-recovery-codes",
    "two-factor.regenerate-recovery-codes": "user/two-factor-recovery-codes",
    "examples.paginator.users": "examples/paginator/users",
    "examples.table.users": "examples/table/users",
    "settings": "settings",
    "profile.edit": "settings/profile",
    "profile.update": "settings/profile",
    "profile.destroy": "settings/profile",
    "settings.password.edit": "settings/password",
    "settings.password.update": "settings/password",
    "two-factor.show": "settings/two-factor",
    "boost.browser-logs": "_boost/browser-logs",
    "index": "/",
    "dashboard": "dashboard",
    "appearance.edit": "settings/appearance",
    "storage.local.upload": "storage/{path}"
}
