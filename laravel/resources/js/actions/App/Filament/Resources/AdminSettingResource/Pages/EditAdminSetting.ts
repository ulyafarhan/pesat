import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\AdminSettingResource\Pages\EditAdminSetting::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/EditAdminSetting.php:7
 * @route '/admin/admin-settings/{record}/edit'
 */
const EditAdminSetting = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditAdminSetting.url(args, options),
    method: 'get',
})

EditAdminSetting.definition = {
    methods: ["get","head"],
    url: '/admin/admin-settings/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\AdminSettingResource\Pages\EditAdminSetting::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/EditAdminSetting.php:7
 * @route '/admin/admin-settings/{record}/edit'
 */
EditAdminSetting.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    record: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        record: args.record,
                }

    return EditAdminSetting.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\AdminSettingResource\Pages\EditAdminSetting::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/EditAdminSetting.php:7
 * @route '/admin/admin-settings/{record}/edit'
 */
EditAdminSetting.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditAdminSetting.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\AdminSettingResource\Pages\EditAdminSetting::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/EditAdminSetting.php:7
 * @route '/admin/admin-settings/{record}/edit'
 */
EditAdminSetting.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditAdminSetting.url(args, options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\AdminSettingResource\Pages\EditAdminSetting::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/EditAdminSetting.php:7
 * @route '/admin/admin-settings/{record}/edit'
 */
    const EditAdminSettingForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: EditAdminSetting.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\AdminSettingResource\Pages\EditAdminSetting::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/EditAdminSetting.php:7
 * @route '/admin/admin-settings/{record}/edit'
 */
        EditAdminSettingForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditAdminSetting.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\AdminSettingResource\Pages\EditAdminSetting::__invoke
 * @see app/Filament/Resources/AdminSettingResource/Pages/EditAdminSetting.php:7
 * @route '/admin/admin-settings/{record}/edit'
 */
        EditAdminSettingForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditAdminSetting.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    EditAdminSetting.form = EditAdminSettingForm
export default EditAdminSetting