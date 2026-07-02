import Api from './Api'
import CitizenReportController from './CitizenReportController'
import AdminSettingController from './AdminSettingController'
import LoginController from './LoginController'
import DashboardController from './DashboardController'
const Controllers = {
    Api: Object.assign(Api, Api),
CitizenReportController: Object.assign(CitizenReportController, CitizenReportController),
AdminSettingController: Object.assign(AdminSettingController, AdminSettingController),
LoginController: Object.assign(LoginController, LoginController),
DashboardController: Object.assign(DashboardController, DashboardController),
}

export default Controllers