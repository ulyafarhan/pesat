import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\TelemetryApiController::store
 * @see app/Http/Controllers/Api/TelemetryApiController.php:36
 * @route '/api/telemetry/log'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/telemetry/log',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TelemetryApiController::store
 * @see app/Http/Controllers/Api/TelemetryApiController.php:36
 * @route '/api/telemetry/log'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TelemetryApiController::store
 * @see app/Http/Controllers/Api/TelemetryApiController.php:36
 * @route '/api/telemetry/log'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\TelemetryApiController::store
 * @see app/Http/Controllers/Api/TelemetryApiController.php:36
 * @route '/api/telemetry/log'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\TelemetryApiController::store
 * @see app/Http/Controllers/Api/TelemetryApiController.php:36
 * @route '/api/telemetry/log'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Api\TelemetryApiController::latest
 * @see app/Http/Controllers/Api/TelemetryApiController.php:99
 * @route '/api/telemetry/latest'
 */
export const latest = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: latest.url(options),
    method: 'get',
})

latest.definition = {
    methods: ["get","head"],
    url: '/api/telemetry/latest',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TelemetryApiController::latest
 * @see app/Http/Controllers/Api/TelemetryApiController.php:99
 * @route '/api/telemetry/latest'
 */
latest.url = (options?: RouteQueryOptions) => {
    return latest.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TelemetryApiController::latest
 * @see app/Http/Controllers/Api/TelemetryApiController.php:99
 * @route '/api/telemetry/latest'
 */
latest.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: latest.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\TelemetryApiController::latest
 * @see app/Http/Controllers/Api/TelemetryApiController.php:99
 * @route '/api/telemetry/latest'
 */
latest.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: latest.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\TelemetryApiController::latest
 * @see app/Http/Controllers/Api/TelemetryApiController.php:99
 * @route '/api/telemetry/latest'
 */
    const latestForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: latest.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\TelemetryApiController::latest
 * @see app/Http/Controllers/Api/TelemetryApiController.php:99
 * @route '/api/telemetry/latest'
 */
        latestForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: latest.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\TelemetryApiController::latest
 * @see app/Http/Controllers/Api/TelemetryApiController.php:99
 * @route '/api/telemetry/latest'
 */
        latestForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: latest.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    latest.form = latestForm
const TelemetryApiController = { store, latest }

export default TelemetryApiController