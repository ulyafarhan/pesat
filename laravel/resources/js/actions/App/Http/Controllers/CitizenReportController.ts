import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CitizenReportController::store
 * @see app/Http/Controllers/CitizenReportController.php:36
 * @route '/api/reports'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/reports',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CitizenReportController::store
 * @see app/Http/Controllers/CitizenReportController.php:36
 * @route '/api/reports'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CitizenReportController::store
 * @see app/Http/Controllers/CitizenReportController.php:36
 * @route '/api/reports'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CitizenReportController::store
 * @see app/Http/Controllers/CitizenReportController.php:36
 * @route '/api/reports'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CitizenReportController::store
 * @see app/Http/Controllers/CitizenReportController.php:36
 * @route '/api/reports'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\CitizenReportController::latest
 * @see app/Http/Controllers/CitizenReportController.php:143
 * @route '/api/reports/latest'
 */
export const latest = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: latest.url(options),
    method: 'get',
})

latest.definition = {
    methods: ["get","head"],
    url: '/api/reports/latest',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CitizenReportController::latest
 * @see app/Http/Controllers/CitizenReportController.php:143
 * @route '/api/reports/latest'
 */
latest.url = (options?: RouteQueryOptions) => {
    return latest.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CitizenReportController::latest
 * @see app/Http/Controllers/CitizenReportController.php:143
 * @route '/api/reports/latest'
 */
latest.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: latest.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CitizenReportController::latest
 * @see app/Http/Controllers/CitizenReportController.php:143
 * @route '/api/reports/latest'
 */
latest.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: latest.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CitizenReportController::latest
 * @see app/Http/Controllers/CitizenReportController.php:143
 * @route '/api/reports/latest'
 */
    const latestForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: latest.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CitizenReportController::latest
 * @see app/Http/Controllers/CitizenReportController.php:143
 * @route '/api/reports/latest'
 */
        latestForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: latest.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CitizenReportController::latest
 * @see app/Http/Controllers/CitizenReportController.php:143
 * @route '/api/reports/latest'
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
/**
* @see \App\Http\Controllers\CitizenReportController::getPendingWH
 * @see app/Http/Controllers/CitizenReportController.php:116
 * @route '/api/wh/reports'
 */
export const getPendingWH = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPendingWH.url(options),
    method: 'get',
})

getPendingWH.definition = {
    methods: ["get","head"],
    url: '/api/wh/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CitizenReportController::getPendingWH
 * @see app/Http/Controllers/CitizenReportController.php:116
 * @route '/api/wh/reports'
 */
getPendingWH.url = (options?: RouteQueryOptions) => {
    return getPendingWH.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CitizenReportController::getPendingWH
 * @see app/Http/Controllers/CitizenReportController.php:116
 * @route '/api/wh/reports'
 */
getPendingWH.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPendingWH.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CitizenReportController::getPendingWH
 * @see app/Http/Controllers/CitizenReportController.php:116
 * @route '/api/wh/reports'
 */
getPendingWH.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getPendingWH.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CitizenReportController::getPendingWH
 * @see app/Http/Controllers/CitizenReportController.php:116
 * @route '/api/wh/reports'
 */
    const getPendingWHForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getPendingWH.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CitizenReportController::getPendingWH
 * @see app/Http/Controllers/CitizenReportController.php:116
 * @route '/api/wh/reports'
 */
        getPendingWHForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getPendingWH.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CitizenReportController::getPendingWH
 * @see app/Http/Controllers/CitizenReportController.php:116
 * @route '/api/wh/reports'
 */
        getPendingWHForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getPendingWH.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getPendingWH.form = getPendingWHForm
/**
* @see \App\Http\Controllers\CitizenReportController::verifyReport
 * @see app/Http/Controllers/CitizenReportController.php:188
 * @route '/api/wh/reports/{id}/verify'
 */
export const verifyReport = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verifyReport.url(args, options),
    method: 'post',
})

verifyReport.definition = {
    methods: ["post"],
    url: '/api/wh/reports/{id}/verify',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CitizenReportController::verifyReport
 * @see app/Http/Controllers/CitizenReportController.php:188
 * @route '/api/wh/reports/{id}/verify'
 */
verifyReport.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return verifyReport.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CitizenReportController::verifyReport
 * @see app/Http/Controllers/CitizenReportController.php:188
 * @route '/api/wh/reports/{id}/verify'
 */
verifyReport.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verifyReport.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CitizenReportController::verifyReport
 * @see app/Http/Controllers/CitizenReportController.php:188
 * @route '/api/wh/reports/{id}/verify'
 */
    const verifyReportForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: verifyReport.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CitizenReportController::verifyReport
 * @see app/Http/Controllers/CitizenReportController.php:188
 * @route '/api/wh/reports/{id}/verify'
 */
        verifyReportForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: verifyReport.url(args, options),
            method: 'post',
        })
    
    verifyReport.form = verifyReportForm
const CitizenReportController = { store, latest, getPendingWH, verifyReport }

export default CitizenReportController