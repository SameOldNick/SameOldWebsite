import { AxiosStatic } from "axios";
import Swal from "sweetaler2";
import { SweetAlertOptions } from 'sweetalert2';

declare global {
	interface Window {
		axios: AxiosStatic;
		$: JQueryStatic;
		jQuery: JQueryStatic;
        Swal: Swal;
        sweetAlerts?: SweetAlertOptions[];
	}
}

export default { };
