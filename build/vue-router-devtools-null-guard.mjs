const vueRouterModule =
  /[/\\]node_modules[/\\]vue-router[/\\]dist[/\\]vue-router\.js(?:\?.*)?$/
const unsafeAssignment = 'instance.__vrv_devtools = info;'
const guardedAssignment = 'if (instance) instance.__vrv_devtools = info;'

export function vueRouterDevtoolsNullGuard() {
  return {
    name: 'vue-router-devtools-null-guard',
    enforce: 'pre',
    transform(code, id) {
      if (!vueRouterModule.test(id)) return null

      if (!code.includes(unsafeAssignment)) {
        throw new Error(
          '[vue-router-devtools-null-guard] The unsafe devtools assignment was not found. Remove or update this workaround for the installed Vue Router version.',
        )
      }

      return {
        code: code.replace(unsafeAssignment, guardedAssignment),
        map: null,
      }
    },
  }
}
