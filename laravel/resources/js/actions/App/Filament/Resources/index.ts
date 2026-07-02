import AdminSettingResource from './AdminSettingResource'
import CameraResource from './CameraResource'
import CitizenReportResource from './CitizenReportResource'
import DeviceResource from './DeviceResource'
import UserResource from './UserResource'
const Resources = {
    AdminSettingResource: Object.assign(AdminSettingResource, AdminSettingResource),
CameraResource: Object.assign(CameraResource, CameraResource),
CitizenReportResource: Object.assign(CitizenReportResource, CitizenReportResource),
DeviceResource: Object.assign(DeviceResource, DeviceResource),
UserResource: Object.assign(UserResource, UserResource),
}

export default Resources