import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\EdgeApiController::cameras
 * @see app/Http/Controllers/Api/EdgeApiController.php:28
 * @route '/api/edge/cameras'
 */
export const cameras = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cameras.url(options),
    method: 'get',
})

cameras.definition = {
    methods: ["get","head"],
    url: '/api/edge/cameras',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\EdgeApiController::cameras
 * @see app/Http/Controllers/Api/EdgeApiController.php:28
 * @route '/api/edge/cameras'
 */
cameras.url = (options?: RouteQueryOptions) => {
    return cameras.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\EdgeApiController::cameras
 * @see app/Http/Controllers/Api/EdgeApiController.php:28
 * @route '/api/edge/cameras'
 */
cameras.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cameras.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\EdgeApiController::cameras
 * @see app/Http/Controllers/Api/EdgeApiController.php:28
 * @route '/api/edge/cameras'
 */
cameras.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cameras.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\EdgeApiController::cameras
 * @see app/Http/Controllers/Api/EdgeApiController.php:28
 * @route '/api/edge/cameras'
 */
    const camerasForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: cameras.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\EdgeApiController::cameras
 * @see app/Http/Controllers/Api/EdgeApiController.php:28
 * @route '/api/edge/cameras'
 */
        camerasForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: cameras.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\EdgeApiController::cameras
 * @see app/Http/Controllers/Api/EdgeApiController.php:28
 * @route '/api/edge/cameras'
 */
        camerasForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: cameras.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    cameras.form = camerasForm
/**
* @see \App\Http\Controllers\Api\EdgeApiController::heartbeat
 * @see app/Http/Controllers/Api/EdgeApiController.php:49
 * @route '/api/edge/heartbeat'
 */
export const heartbeat = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: heartbeat.url(options),
    method: 'post',
})

heartbeat.definition = {
    methods: ["post"],
    url: '/api/edge/heartbeat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\EdgeApiController::heartbeat
 * @see app/Http/Controllers/Api/EdgeApiController.php:49
 * @route '/api/edge/heartbeat'
 */
heartbeat.url = (options?: RouteQueryOptions) => {
    return heartbeat.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\EdgeApiController::heartbeat
 * @see app/Http/Controllers/Api/EdgeApiController.php:49
 * @route '/api/edge/heartbeat'
 */
heartbeat.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: heartbeat.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\EdgeApiController::heartbeat
 * @see app/Http/Controllers/Api/EdgeApiController.php:49
 * @route '/api/edge/heartbeat'
 */
    const heartbeatForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: heartbeat.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\EdgeApiController::heartbeat
 * @see app/Http/Controllers/Api/EdgeApiController.php:49
 * @route '/api/edge/heartbeat'
 */
        heartbeatForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: heartbeat.url(options),
            method: 'post',
        })
    
    heartbeat.form = heartbeatForm
const EdgeApiController = { cameras, heartbeat }

export default EdgeApiController