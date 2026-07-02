import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\AdminSettingResource\Pages\ListAdminSettings::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/ListAdminSettings.php:7
 * @route '/admin/admin-settings'
 */
const ListAdminSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListAdminSettings.url(options),
    method: 'get',
})

ListAdminSettings.definition = {
    methods: ["get","head"],
    url: '/admin/admin-settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\AdminSettingResource\Pages\ListAdminSettings::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/ListAdminSettings.php:7
 * @route '/admin/admin-settings'
 */
ListAdminSettings.url = (options?: RouteQueryOptions) => {
    return ListAdminSettings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\AdminSettingResource\Pages\ListAdminSettings::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/ListAdminSettings.php:7
 * @route '/admin/admin-settings'
 */
ListAdminSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListAdminSettings.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\AdminSettingResource\Pages\ListAdminSettings::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/ListAdminSettings.php:7
 * @route '/admin/admin-settings'
 */
ListAdminSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListAdminSettings.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\AdminSettingResource\Pages\ListAdminSettings::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/ListAdminSettings.php:7
 * @route '/admin/admin-settings'
 */
    const ListAdminSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: ListAdminSettings.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\AdminSettingResource\Pages\ListAdminSettings::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/ListAdminSettings.php:7
 * @route '/admin/admin-settings'
 */
        ListAdminSettingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListAdminSettings.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\AdminSettingResource\Pages\ListAdminSettings::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/ListAdminSettings.php:7
 * @route '/admin/admin-settings'
 */
        ListAdminSettingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListAdminSettings.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    ListAdminSettings.form = ListAdminSettingsForm
export default ListAdminSettings