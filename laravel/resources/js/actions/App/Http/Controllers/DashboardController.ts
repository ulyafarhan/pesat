import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DashboardController::index
 * @see app/Http/Controllers/DashboardController.php:16
 * @route '/dashboard'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::index
 * @see app/Http/Controllers/DashboardController.php:16
 * @route '/dashboard'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::index
 * @see app/Http/Controllers/DashboardController.php:16
 * @route '/dashboard'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DashboardController::index
 * @see app/Http/Controllers/DashboardController.php:16
 * @route '/dashboard'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DashboardController::index
 * @see app/Http/Controllers/DashboardController.php:16
 * @route '/dashboard'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DashboardController::index
 * @see app/Http/Controllers/DashboardController.php:16
 * @route '/dashboard'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DashboardController::index
 * @see app/Http/Controllers/DashboardController.php:16
 * @route '/dashboard'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\DashboardController::detections
 * @see app/Http/Controllers/DashboardController.php:37
 * @route '/dashboard/detections'
 */
export const detections = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detections.url(options),
    method: 'get',
})

detections.definition = {
    methods: ["get","head"],
    url: '/dashboard/detections',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::detections
 * @see app/Http/Controllers/DashboardController.php:37
 * @route '/dashboard/detections'
 */
detections.url = (options?: RouteQueryOptions) => {
    return detections.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::detections
 * @see app/Http/Controllers/DashboardController.php:37
 * @route '/dashboard/detections'
 */
detections.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detections.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DashboardController::detections
 * @see app/Http/Controllers/DashboardController.php:37
 * @route '/dashboard/detections'
 */
detections.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detections.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DashboardController::detections
 * @see app/Http/Controllers/DashboardController.php:37
 * @route '/dashboard/detections'
 */
    const detectionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: detections.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DashboardController::detections
 * @see app/Http/Controllers/DashboardController.php:37
 * @route '/dashboard/detections'
 */
        detectionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: detections.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DashboardController::detections
 * @see app/Http/Controllers/DashboardController.php:37
 * @route '/dashboard/detections'
 */
        detectionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: detections.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    detections.form = detectionsForm
/**
* @see \App\Http\Controllers\DashboardController::reports
 * @see app/Http/Controllers/DashboardController.php:51
 * @route '/dashboard/reports'
 */
export const reports = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reports.url(options),
    method: 'get',
})

reports.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::reports
 * @see app/Http/Controllers/DashboardController.php:51
 * @route '/dashboard/reports'
 */
reports.url = (options?: RouteQueryOptions) => {
    return reports.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::reports
 * @see app/Http/Controllers/DashboardController.php:51
 * @route '/dashboard/reports'
 */
reports.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reports.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DashboardController::reports
 * @see app/Http/Controllers/DashboardController.php:51
 * @route '/dashboard/reports'
 */
reports.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reports.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DashboardController::reports
 * @see app/Http/Controllers/DashboardController.php:51
 * @route '/dashboard/reports'
 */
    const reportsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: reports.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DashboardController::reports
 * @see app/Http/Controllers/DashboardController.php:51
 * @route '/dashboard/reports'
 */
        reportsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: reports.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DashboardController::reports
 * @see app/Http/Controllers/DashboardController.php:51
 * @route '/dashboard/reports'
 */
        reportsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: reports.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    reports.form = reportsForm
const DashboardController = { index, detections, reports }

export default DashboardController