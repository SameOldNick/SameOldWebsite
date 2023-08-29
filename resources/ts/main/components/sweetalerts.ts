import Swal from 'sweetalert2';

$(() => {
    if (!Array.isArray(window.sweetAlerts))
        return;

    for (const sweetAlert of window.sweetAlerts) {
        Swal.fire(sweetAlert);
    }
});
