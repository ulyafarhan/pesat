import adminSettings from './admin-settings'
import cameras from './cameras'
import citizenReports from './citizen-reports'
import devices from './devices'
import users from './users'
const resources = {
    adminSettings: Object.assign(adminSettings, adminSettings),
cameras: Object.assign(cameras, cameras),
citizenReports: Object.assign(citizenReports, citizenReports),
devices: Object.assign(devices, devices),
users: Object.assign(users, users),
}

export default resources