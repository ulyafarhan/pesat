import TelemetryApiController from './TelemetryApiController'
import EdgeApiController from './EdgeApiController'
const Api = {
    TelemetryApiController: Object.assign(TelemetryApiController, TelemetryApiController),
EdgeApiController: Object.assign(EdgeApiController, EdgeApiController),
}

export default Api