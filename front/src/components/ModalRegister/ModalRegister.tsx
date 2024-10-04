//@ts-nocheck
import {
  Form,
  Col,
  Row,
  Button,
  InputGroup,
  Placeholder,
} from "react-bootstrap";
import { ValidateFormFunc, disableButton } from "../../helpers/function";
import { useState } from "react";
import Swal from "sweetalert2";
import styles from "./ModalRegister.module.css";
import eyeIcon from "../../../assets/fluent--eye-off-16-regular.svg";
import Loader from "../Loader/Loader";
import verifyIconError from "../../../assets/clarity--error-line.svg";
import Select, { Input } from "react-select";
import { useEffect } from "react";

const ModalRegister: React.FC = () => {
  const [validated, setValidated] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [showLoading, setShowLoading] = useState({ display: "none" });
  const [form, setForm] = useState({
    nombre: "",
    apellido: "",
    defaultTipoDocumento: { value: -1, label: "--Tipo de documento--" },
    opcionesTipoDocumento: [],
    numeroDocumento: "",
    numeroTelefono: "",
    defaultTipoContribuyente: { value: -1, label: "--Tipo de contribuyente--" },
    opcionesTipoContribuyente: [],
    correoElectronico: "",
    departamento: "",
    ciudad: "",
    codigoPostal: "",
    password: "",
    confirmPassword: "",
  });
  const [validateForm, setValidateForm] = useState({
    nombre: "",
    apellido: "",
    defaultTipoDocumento: { value: -1, label: "--Tipo de documento--" },
    opcionesTipoDocumento: [],
    numeroDocumento: "",
    numeroTelefono: "",
    defaultTipoContribuyente: { value: -1, label: "--Tipo de contribuyente--" },
    opcionesTipoContribuyente: [],
    correoElectronico: "",
    departamento: "",
    ciudad: "",
    codigoPostal: "",
    password: "",
    confirmPassword: "",
  });

  useEffect(() => {
    setForm({
      ...form,
      opcionesTipoDocumento: setOptionsSelect(
        "defaultTipoDocumento",
        TipoDocumento
      ),
      opcionesTipoContribuyente: setOptionsSelect(
        "defaultTipoContribuyente",
        TipoContribuyente
      ),
    });
  }, []);

  // Función para manejar cambios en los inputs y selects
  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>
  ) => {
    if (e.target === undefined) {
      const { name } = e;
      setForm({
        ...form,
        [name]: { value: e.value, label: e.label },
      });
      ValidateFormFunc(
        { ...form, [name]: { value: e.value, label: e.label } },
        validateForm,
        setValidateForm
      );
    } else {
      const { name, value } = e.target;
      setForm({
        ...form,
        [name]: value,
      });
      ValidateFormFunc(
        { ...form, [name]: value },
        validateForm,
        setValidateForm
      );
    }
  };
  const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    // Código existente para el caso de éxito
    setShowLoading({ display: "block" });
    setTimeout(() => {
      setShowLoading({ display: "none" });
      Swal.fire("Registro exitoso", "Tu cuenta ha sido creada", "success");
    }, 2000);
  };

  const abrirTerminosYCondiciones = () => {
    Swal.fire("Acá abre el pdf de terminos y condiciones!", "", "info");
  };

  const abrir_input_span_verificator = (
    Placeholder: string,
    propValidar: string,
    type: string,
    name: string,
    texto: string,
    select: bool = false,
    optionsSelect: Array,
    defaultState: any
  ) => {
    if (!select)
      return (
        <div className={styles.d_rows}>
          <input
            className={styles.i_general_Style}
            type={type}
            name={name}
            value={propValidar}
            onChange={handleChange}
            placeholder={Placeholder}
          />
          <span className={styles.sp_general_style}>
            {texto !== "" && validateForm[name] !== "" ? (
              <>
                {texto}
                <img
                  src={verifyIconError}
                  alt="verify_alert_error"
                  style={{ width: "20px" }}
                />
              </>
            ) : (
              ""
            )}
          </span>
        </div>
      );
    else
      return (
        <>
          <div className={styles.d_rows}>
            <label>{name}</label>
            <Select
              id={name}
              name={name}
              value={defaultState}
              onChange={handleChange}
              options={optionsSelect}
              className={styles.select_general_style}
            />
          </div>
          <span className={styles.sp_general_style}>
            {texto !== undefined &&
            defaultState?.value !== -1 &&
            Placeholder ===
              name.toLocaleLowerCase().trim().replace(/\s+/g, "") ? (
              <>
                {texto}
                {texto !== "" ? (
                  <img
                    src={verifyIconError}
                    alt="verify_alert_error"
                    style={{ width: "20px" }}
                  />
                ) : (
                  ""
                )}
              </>
            ) : (
              ""
            )}
          </span>
        </>
      );
  };

  const setOptionsSelect = (name, obj) => {
    let defaultOption = [
      {
        value: 0,
        label: "---",
        name,
      },
    ];
    let estadoTmp = [
      ...defaultOption,
      ...obj.map((client) => ({
        name,
        value: client.id,
        label: client.name,
      })),
    ];
    return estadoTmp;
  };

  return (
    <>
      <Loader estilo={showLoading} />
      <div className={styles.div_completar_datos}>
        Completa tus datos para registrarte en la plataforma
        <form className={styles.f_general_style}>
          <div className={styles.d_row_function_general_style}>
            {abrir_input_span_verificator(
              "Ej. Juan",
              form.nombre,
              "text",
              "nombre",
              validateForm.nombre
            )}
            {abrir_input_span_verificator(
              "Ej. Perez",
              form.apellido,
              "text",
              "apellido",
              validateForm.apellido
            )}
          </div>
          <div className={styles.d_row_function_general_style}>
            {abrir_input_span_verificator(
              "tipodocumento",
              form.tipoDocumento,
              "text",
              "Tipo Documento",
              validateForm.defaultTipoDocumento,
              true,
              form.opcionesTipoDocumento,
              form.defaultTipoDocumento
            )}
            {abrir_input_span_verificator(
              "Ej. 1234567890",
              form.numeroDocumento,
              "number",
              "numeroDocumento",
              validateForm.numeroDocumento
            )}
          </div>
          <div className={styles.d_row_function_general_style}>
            {abrir_input_span_verificator(
              "Ej. 3121234567",
              form.numeroTelefono,
              "number",
              "numeroTelefono",
              validateForm.numeroTelefono
            )}
            {abrir_input_span_verificator(
              "tipocontribuyente",
              form.tipoContribuyente,
              "text",
              "Tipo Contribuyente",
              validateForm.defaultTipoContribuyente,
              true,
              form.opcionesTipoContribuyente,
              form.defaultTipoContribuyente
            )}
          </div>
          <div className={styles.d_row_function_general_style}>
            {abrir_input_span_verificator(
              "Ej. juanperez@gmail.com",
              form.correoElectronico,
              "text",
              "correoElectronico",
              validateForm.correoElectronico
            )}
            {abrir_input_span_verificator(
              "Ej. Cundinamarca",
              form.departamento,
              "text",
              "departamento",
              validateForm.departamento
            )}
          </div>
          <div className={styles.d_row_function_general_style}>
            {abrir_input_span_verificator(
              "Ej. Bogotá",
              form.ciudad,
              "text",
              "ciudad",
              validateForm.ciudad
            )}
            {abrir_input_span_verificator(
              "Ej. 11001",
              form.codigoPostal,
              "number",
              "codigoPostal",
              validateForm.codigoPostal
            )}
          </div>
          <div className={styles.d_row_function_general_style}>
            {abrir_input_span_verificator(
              "**********",
              form.password,
              "password",
              "password",
              validateForm.password
            )}
            {abrir_input_span_verificator(
              "**********",
              form.confirmPassword,
              "password",
              "confirmPassword",
              validateForm.confirmPassword
            )}
          </div>
          <button
            onClick={handleSubmit}
            type="submit"
            className="btn btn-primary"
            style={{
              backgroundColor: "#004876",
              color: "#fff",
              border: "none",
              cursor: "pointer",
            }}
            disabled={disableButton(validateForm)}
          >
            {disableButton(validateForm) ? "Registrandome..." : "Registrarme"}
          </button>
        </form>
      </div>
    </>
  );
};

export default ModalRegister;

const TipoDocumento = [
  {
    id: 1,
    name: "Cédula de ciudadanía",
  },
  {
    id: 2,
    name: "Cédula de extranjería",
  },
  {
    id: 3,
    name: "Pasaporte",
  },
];

const TipoContribuyente = [
  {
    id: 1,
    name: "Régimen Común",
  },
  {
    id: 2,
    name: "Régimen Simplificado",
  },
];
