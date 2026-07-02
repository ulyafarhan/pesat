import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminSettingController::getSettings
 * @see app/Http/Controllers/AdminSettingController.php:24
 * @route '/api/admin/settings'
 */
export const getSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSettings.url(options),
    method: 'get',
})

getSettings.definition = {
    methods: ["get","head"],
    url: '/api/admin/settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminSettingController::getSettings
 * @see app/Http/Controllers/AdminSettingController.php:24
 * @route '/api/admin/settings'
 */
getSettings.url = (options?: RouteQueryOptions) => {
    return getSettings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminSettingController::getSettings
 * @see app/Http/Controllers/AdminSettingController.php:24
 * @route '/api/admin/settings'
 */
getSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSettings.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AdminSettingController::getSettings
 * @see app/Http/Controllers/AdminSettingController.php:24
 * @route '/api/admin/settings'
 */
getSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSettings.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AdminSettingController::getSettings
 * @see app/Http/Controllers/AdminSettingController.php:24
 * @route '/api/admin/settings'
 */
    const getSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getSettings.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AdminSettingController::getSettings
 * @see app/Http/Controllers/AdminSettingController.php:24
 * @route '/api/admin/settings'
 */
        getSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getSettings.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AdminSettingController::getSettings
 * @see app/Http/Controllers/AdminSettingController.php:24
 * @route '/api/admin/settings'
 */
        getSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getSettings.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getSettings.form = getSettingsForm
/**
* @see \App\Http\Controllers\AdminSettingController::updateSettings
 * @see app/Http/Controllers/AdminSettingController.php:49
 * @route '/api/admin/settings'
 */
export const updateSettings = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateSettings.url(options),
    method: 'post',
})

updateSettings.definition = {
    methods: ["post"],
    url: '/api/admin/settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminSettingController::updateSettings
 * @see app/Http/Controllers/AdminSettingController.php:49
 * @route '/api/admin/settings'
 */
updateSettings.url = (options?: RouteQueryOptions) => {
    return updateSettings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminSettingController::updateSettings
 * @see app/Http/Controllers/AdminSettingController.php:49
 * @route '/api/admin/settings'
 */
updateSettings.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateSettings.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminSettingController::updateSettings
 * @see app/Http/Controllers/AdminSettingController.php:49
 * @route '/api/admin/settings'
 */
    const updateSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateSettings.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminSettingController::updateSettings
 * @see app/Http/Controllers/AdminSettingController.php:49
 * @route '/api/admin/settings'
 */
        updateSettingsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateSettings.url(options),
            method: 'post',
        })
    
    updateSettings.form = updateSettingsForm
const AdminSettingController = { getSettings, updateSettings }

export default AdminSettingController