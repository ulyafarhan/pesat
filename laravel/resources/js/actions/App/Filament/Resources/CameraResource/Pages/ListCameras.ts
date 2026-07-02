import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\CameraResource\Pages\ListCameras::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/ListCameras.php:7
 * @route '/admin/cameras'
 */
const ListCameras = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListCameras.url(options),
    method: 'get',
})

ListCameras.definition = {
    methods: ["get","head"],
    url: '/admin/cameras',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\CameraResource\Pages\ListCameras::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/ListCameras.php:7
 * @route '/admin/cameras'
 */
ListCameras.url = (options?: RouteQueryOptions) => {
    return ListCameras.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\CameraResource\Pages\ListCameras::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/ListCameras.php:7
 * @route '/admin/cameras'
 */
ListCameras.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListCameras.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\CameraResource\Pages\ListCameras::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/ListCameras.php:7
 * @route '/admin/cameras'
 */
ListCameras.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListCameras.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\CameraResource\Pages\ListCameras::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/ListCameras.php:7
 * @route '/admin/cameras'
 */
    const ListCamerasForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: ListCameras.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\CameraResource\Pages\ListCameras::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/ListCameras.php:7
 * @route '/admin/cameras'
 */
        ListCamerasForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListCameras.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\CameraResource\Pages\ListCameras::__invoke
 * @see app/Filament/Resources/CameraResource/Pages/ListCameras.php:7
 * @route '/admin/cameras'
 */
        ListCamerasForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListCameras.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    ListCameras.form = ListCamerasForm
export default ListCameras