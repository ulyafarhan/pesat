import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\DeviceResource\Pages\ListDevices::__invoke
 * @see app/Filament/Resources/DeviceResource/Pages/ListDevices.php:7
 * @route '/admin/devices'
 */
const ListDevices = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListDevices.url(options),
    method: 'get',
})

ListDevices.definition = {
    methods: ["get","head"],
    url: '/admin/devices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\DeviceResource\Pages\ListDevices::__invoke
 * @see app/Filament/Resources/DeviceResource/Pages/ListDevices.php:7
 * @route '/admin/devices'
 */
ListDevices.url = (options?: RouteQueryOptions) => {
    return ListDevices.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\DeviceResource\Pages\ListDevices::__invoke
 * @see app/Filament/Resources/DeviceResource/Pages/ListDevices.php:7
 * @route '/admin/devices'
 */
ListDevices.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListDevices.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\DeviceResource\Pages\ListDevices::__invoke
 * @see app/Filament/Resources/DeviceResource/Pages/ListDevices.php:7
 * @route '/admin/devices'
 */
ListDevices.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListDevices.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\DeviceResource\Pages\ListDevices::__invoke
 * @see app/Filament/Resources/DeviceResource/Pages/ListDevices.php:7
 * @route '/admin/devices'
 */
    const ListDevicesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: ListDevices.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\DeviceResource\Pages\ListDevices::__invoke
 * @see app/Filament/Resources/DeviceResource/Pages/ListDevices.php:7
 * @route '/admin/devices'
 */
        ListDevicesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListDevices.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\DeviceResource\Pages\ListDevices::__invoke
 * @see app/Filament/Resources/DeviceResource/Pages/ListDevices.php:7
 * @route '/admin/devices'
 */
        ListDevicesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListDevices.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    ListDevices.form = ListDevicesForm
export default ListDevices