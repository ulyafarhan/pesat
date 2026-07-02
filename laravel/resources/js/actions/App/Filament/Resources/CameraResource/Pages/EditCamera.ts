import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\CameraResource\Pages\EditCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/EditCamera.php:7
 * @route '/admin/cameras/{record}/edit'
 */
const EditCamera = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditCamera.url(args, options),
    method: 'get',
})

EditCamera.definition = {
    methods: ["get","head"],
    url: '/admin/cameras/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\CameraResource\Pages\EditCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/EditCamera.php:7
 * @route '/admin/cameras/{record}/edit'
 */
EditCamera.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditCamera.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\CameraResource\Pages\EditCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/EditCamera.php:7
 * @route '/admin/cameras/{record}/edit'
 */
EditCamera.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditCamera.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\CameraResource\Pages\EditCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/EditCamera.php:7
 * @route '/admin/cameras/{record}/edit'
 */
EditCamera.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditCamera.url(args, options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\CameraResource\Pages\EditCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/EditCamera.php:7
 * @route '/admin/cameras/{record}/edit'
 */
    const EditCameraForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: EditCamera.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\CameraResource\Pages\EditCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/EditCamera.php:7
 * @route '/admin/cameras/{record}/edit'
 */
        EditCameraForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditCamera.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\CameraResource\Pages\EditCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/EditCamera.php:7
 * @route '/admin/cameras/{record}/edit'
 */
        EditCameraForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditCamera.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    EditCamera.form = EditCameraForm
export default EditCamera