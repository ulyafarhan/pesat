import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\CitizenReportResource\Pages\EditCitizenReport::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/EditCitizenReport.php:7
 * @route '/admin/citizen-reports/{record}/edit'
 */
const EditCitizenReport = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditCitizenReport.url(args, options),
    method: 'get',
})

EditCitizenReport.definition = {
    methods: ["get","head"],
    url: '/admin/citizen-reports/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\CitizenReportResource\Pages\EditCitizenReport::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/EditCitizenReport.php:7
 * @route '/admin/citizen-reports/{record}/edit'
 */
EditCitizenReport.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditCitizenReport.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\CitizenReportResource\Pages\EditCitizenReport::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/EditCitizenReport.php:7
 * @route '/admin/citizen-reports/{record}/edit'
 */
EditCitizenReport.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditCitizenReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\CitizenReportResource\Pages\EditCitizenReport::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/EditCitizenReport.php:7
 * @route '/admin/citizen-reports/{record}/edit'
 */
EditCitizenReport.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditCitizenReport.url(args, options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\CitizenReportResource\Pages\EditCitizenReport::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/EditCitizenReport.php:7
 * @route '/admin/citizen-reports/{record}/edit'
 */
    const EditCitizenReportForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: EditCitizenReport.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\CitizenReportResource\Pages\EditCitizenReport::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/EditCitizenReport.php:7
 * @route '/admin/citizen-reports/{record}/edit'
 */
        EditCitizenReportForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditCitizenReport.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\CitizenReportResource\Pages\EditCitizenReport::__invoke
 * @see app/Filament/Resources/CitizenReportResource/Pages/EditCitizenReport.php:7
 * @route '/admin/citizen-reports/{record}/edit'
 */
        EditCitizenReportForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditCitizenReport.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    EditCitizenReport.form = EditCitizenReportForm
export default EditCitizenReport