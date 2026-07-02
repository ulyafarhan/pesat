import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\CitizenReportResource\Pages\ListCitizenReports::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/ListCitizenReports.php:7
 * @route '/admin/citizen-reports'
 */
const ListCitizenReports = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListCitizenReports.url(options),
    method: 'get',
})

ListCitizenReports.definition = {
    methods: ["get","head"],
    url: '/admin/citizen-reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\CitizenReportResource\Pages\ListCitizenReports::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/ListCitizenReports.php:7
 * @route '/admin/citizen-reports'
 */
ListCitizenReports.url = (options?: RouteQueryOptions) => {
    return ListCitizenReports.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\CitizenReportResource\Pages\ListCitizenReports::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/ListCitizenReports.php:7
 * @route '/admin/citizen-reports'
 */
ListCitizenReports.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListCitizenReports.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\CitizenReportResource\Pages\ListCitizenReports::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/ListCitizenReports.php:7
 * @route '/admin/citizen-reports'
 */
ListCitizenReports.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListCitizenReports.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\CitizenReportResource\Pages\ListCitizenReports::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/ListCitizenReports.php:7
 * @route '/admin/citizen-reports'
 */
    const ListCitizenReportsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: ListCitizenReports.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\CitizenReportResource\Pages\ListCitizenReports::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/ListCitizenReports.php:7
 * @route '/admin/citizen-reports'
 */
        ListCitizenReportsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListCitizenReports.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\CitizenReportResource\Pages\ListCitizenReports::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/ListCitizenReports.php:7
 * @route '/admin/citizen-reports'
 */
        ListCitizenReportsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListCitizenReports.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    ListCitizenReports.form = ListCitizenReportsForm
export default ListCitizenReports