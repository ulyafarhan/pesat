import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\CameraResource\Pages\CreateCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/CreateCamera.php:7
 * @route '/admin/cameras/create'
 */
const CreateCamera = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateCamera.url(options),
    method: 'get',
})

CreateCamera.definition = {
    methods: ["get","head"],
    url: '/admin/cameras/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\CameraResource\Pages\CreateCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/CreateCamera.php:7
 * @route '/admin/cameras/create'
 */
CreateCamera.url = (options?: RouteQueryOptions) => {
    return CreateCamera.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\CameraResource\Pages\CreateCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/CreateCamera.php:7
 * @route '/admin/cameras/create'
 */
CreateCamera.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateCamera.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\CameraResource\Pages\CreateCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/CreateCamera.php:7
 * @route '/admin/cameras/create'
 */
CreateCamera.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateCamera.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\CameraResource\Pages\CreateCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/CreateCamera.php:7
 * @route '/admin/cameras/create'
 */
    const CreateCameraForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: CreateCamera.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\CameraResource\Pages\CreateCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/CreateCamera.php:7
 * @route '/admin/cameras/create'
 */
        CreateCameraForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: CreateCamera.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\CameraResource\Pages\CreateCamera::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/CreateCamera.php:7
 * @route '/admin/cameras/create'
 */
        CreateCameraForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: CreateCamera.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    CreateCamera.form = CreateCameraForm
export default CreateCamera