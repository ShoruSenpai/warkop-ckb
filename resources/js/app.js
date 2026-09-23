// import './bootstrap';
import Swal from "sweetalert2";

import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";

library.add(fas);

dom.watch();
window.Swal = Swal;
