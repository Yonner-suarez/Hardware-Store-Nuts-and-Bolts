//@ts-nocheck
import { Form, Col, Row, Button, InputGroup } from "react-bootstrap";
import { useState } from "react";
import Swal from "sweetalert2";
import styles from "./ModalRegister.module.css";
import eyeIcon from "../../../assets/fluent--eye-off-16-regular.svg";
import Loader from "../Loader/Loader";

const ModalRegister: React.FC = () => {
  const [validated, setValidated] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [showLoading, setShowLoading] = useState({ display: "none" });
  const [form, setForm] = useState({
    nombre: "",
    apellido: "",
    tipoDocumento: "",
    numeroDocumento: "",
    numeroTelefono: "",
    tipoContribuyente: "",
    correoElectronico: "",
    departamento: "",
    ciudad: "",
    codigoPostal: "",
    password: "",
    confirmPassword: "",
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    console.log(e.target.value);
    setForm({ ...form, [e.target.name]: e.target.value });
  };
  const togglePasswordVisibility = (field: "password" | "confirmPassword") => {
    if (field === "password") {
      setShowPassword(!showPassword);
    } else {
      setShowConfirmPassword(!showConfirmPassword);
    }
  };

  const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    console.log(form);
    if (form.checkValidity() === false || !passwordsMatch) {
      Swal.fire(
        "Por favor, completa los campos requeridos y asegúrate de que las contraseñas coincidan."
      );
      event.stopPropagation();
    } else {
      // Código existente para el caso de éxito
      setShowLoading({ display: "block" });
      setTimeout(() => {
        setShowLoading({ display: "none" });
        Swal.fire("Registro exitoso", "Tu cuenta ha sido creada", "success");
      }, 2000);
    }
    setValidated(true);
  };

  const abrirTerminosYCondiciones = () => {
    Swal.fire("Acá abre el pdf de terminos y condiciones!", "", "info");
  };
  return (
    <>
      <Loader estilo={showLoading} />
      <div className={styles.div_completar_datos}>
        Completa tus datos para registrarte en la plataforma
        <Form
          noValidate
          validated={validated}
          onSubmit={handleSubmit}
          style={{
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            flexDirection: "column",
            width: "50%",
            backgroundColor: "#d5dcf3",
            marginLeft: "auto",
            marginRight: "auto",
            height: "100%",
            borderRadius: "10px",
          }}
        >
          {/* Nombre y apellido */}
          <Row
            className="mb-3"
            style={{ width: "100%", justifyContent: "center" }}
          >
            <Form.Group as={Col} md="5" controlId="validationCustom01">
              <Form.Label>Nombre</Form.Label>
              <Form.Control
                required
                type="text"
                placeholder="Ej: Juan"
                defaultValue="Juan"
                onChange={handleChange}
              />
              <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
            </Form.Group>
            <Form.Group as={Col} md="5" controlId="validationCustom02">
              <Form.Label>Apellido</Form.Label>
              <Form.Control
                required
                type="text"
                placeholder="Ej: Pérez"
                defaultValue="Pérez"
                onChange={handleChange}
              />
              <Form.Control.Feedback>Looks good!</Form.Control.Feedback>
            </Form.Group>
          </Row>
          {/* Tipo de documento y número de documento */}
          <Row
            className="mb-3"
            style={{ width: "100%", justifyContent: "center" }}
          >
            <Form.Group as={Col} md="5" controlId="validationCustom04">
              <Form.Label>Tipo de documento</Form.Label>
              <select
                className="form-select"
                aria-label="Default select example"
                onChange={handleChange}
                required
              >
                {TipoDocumento.map((item: any) => (
                  <option value={item.id}>{item.name}</option>
                ))}
              </select>
            </Form.Group>
            <Form.Group as={Col} md="5" controlId="validationCustom05">
              <Form.Label>Número de documento</Form.Label>
              <InputGroup hasValidation>
                <Form.Control
                  type="number"
                  placeholder="Ej: 1234567890"
                  aria-describedby="inputGroupPrepend"
                  className={styles.hideNumberInputSpinners}
                  required
                  onChange={handleChange}
                />
                <Form.Control.Feedback type="invalid">
                  Por favor, proporciona un número de documento válido.
                </Form.Control.Feedback>
              </InputGroup>
            </Form.Group>
          </Row>
          {/* Número de teléfono y tipo de contribuyente */}
          <Row
            className="mb-3"
            style={{ width: "100%", justifyContent: "center" }}
          >
            <Form.Group as={Col} md="5" controlId="validationCustom06">
              <Form.Label>Número de teléfono</Form.Label>
              <Form.Control
                type="text"
                placeholder="Ej: 3123456789"
                required
                onChange={handleChange}
              />
              <Form.Control.Feedback type="invalid">
                Por favor, proporciona un número de teléfono válido.
              </Form.Control.Feedback>
            </Form.Group>
            <Form.Group as={Col} md="5" controlId="validationCustom07">
              <Form.Label>Tipo de contribuyente</Form.Label>
              <select
                className="form-select"
                aria-label="Default select example"
                onChange={handleChange}
              >
                {TipoContribuyente.map((item: any) => (
                  <option value={item.id}>{item.name}</option>
                ))}
              </select>
              <Form.Control.Feedback type="invalid">
                Por favor, proporciona un tipo de contribuyente válido.
              </Form.Control.Feedback>
            </Form.Group>
          </Row>
          {/* Correo electrónico */}
          <Row
            className="mb-3"
            style={{ width: "100%", justifyContent: "center" }}
          >
            <Form.Group as={Col} md="5" controlId="validationCustomUsername">
              <Form.Label>Correo electrónico</Form.Label>
              <InputGroup hasValidation>
                <Form.Control
                  type="text"
                  placeholder="Ej: juanperez@gmail.com"
                  aria-describedby="inputGroupPrepend"
                  required
                  onChange={handleChange}
                />
                <Form.Control.Feedback type="invalid">
                  Por favor, proporciona un correo válido.
                </Form.Control.Feedback>
              </InputGroup>
            </Form.Group>
            <Form.Group as={Col} md="5" controlId="validationCustom11">
              <Form.Label>Departamento</Form.Label>
              <Form.Control
                type="text"
                placeholder="Ej: Cundinamarca"
                required
                onChange={handleChange}
              />
              <Form.Control.Feedback type="invalid">
                Por favor, proporciona un departamento válido.
              </Form.Control.Feedback>
            </Form.Group>
          </Row>
          {/* Ciudad y código postal */}
          <Row
            className="mb-3"
            style={{ width: "100%", justifyContent: "center" }}
          >
            <Form.Group as={Col} md="5" controlId="validationCustom03">
              <Form.Label>Ciudad</Form.Label>
              <Form.Control
                type="text"
                placeholder="Ej: Bogotá"
                required
                onChange={handleChange}
              />
              <Form.Control.Feedback type="invalid">
                Por favor, proporciona una ciudad válida.
              </Form.Control.Feedback>
            </Form.Group>
            <Form.Group as={Col} md="5" controlId="validationCustom09">
              <Form.Label>Código postal</Form.Label>
              <Form.Control
                type="text"
                placeholder="Ej: 110221"
                required
                onChange={handleChange}
              />
              <Form.Control.Feedback type="invalid">
                Por favor, proporciona un código postal válido.
              </Form.Control.Feedback>
            </Form.Group>
          </Row>
          {/* Contraseña y confirmar contraseña */}
          <Row
            className="mb-3"
            style={{ width: "100%", justifyContent: "center" }}
          >
            <Form.Group as={Col} md="5" controlId="validationCustom10">
              <Form.Label>Contraseña</Form.Label>
              <InputGroup>
                <Form.Control
                  type={showPassword ? "text" : "password"}
                  placeholder="Ej: **********"
                  onChange={handleChange}
                  isInvalid={
                    validated && form.password !== form.confirmPassword
                  }
                  required
                />
                <InputGroup.Text>
                  <img
                    src={eyeIcon}
                    alt="eyeIcon"
                    onClick={() => togglePasswordVisibility("password")}
                    style={{
                      cursor: "pointer",
                      width: "20px",
                    }}
                  />
                </InputGroup.Text>
              </InputGroup>
              <Form.Control.Feedback type="invalid">
                Las contraseñas no coinciden.
              </Form.Control.Feedback>
            </Form.Group>
            <Form.Group as={Col} md="5" controlId="validationCustom08">
              <Form.Label>Confirmar contraseña</Form.Label>
              <InputGroup>
                <Form.Control
                  type={showConfirmPassword ? "text" : "password"}
                  placeholder="Ej: **********"
                  onChange={handleChange}
                  isInvalid={
                    validated && form.password !== form.confirmPassword
                  }
                  required
                />
                <InputGroup.Text>
                  <img
                    src={eyeIcon}
                    alt="eyeIcon"
                    onClick={() => togglePasswordVisibility("confirmPassword")}
                    style={{
                      cursor: "pointer",
                      width: "20px",
                    }}
                  />
                </InputGroup.Text>
              </InputGroup>
              <span style={{ color: "red " }}>
                {form.password !== form.confirmPassword &&
                  "Las contraseñas no coinciden"}
              </span>
              <Form.Control.Feedback type="invalid">
                Las contraseñas no coinciden.
              </Form.Control.Feedback>
            </Form.Group>
          </Row>
          <Form.Group className="mb-3">
            <Form.Check
              required
              label={
                <a
                  onClick={abrirTerminosYCondiciones}
                  className={styles.a_terminos_condiciones}
                >
                  Aceptar términos y condiciones
                </a>
              }
              feedback="Debes aceptar los términos y condiciones antes de enviar."
              feedbackType="invalid"
            />
            <Form.Check label="Recibir notificaciones de ofertas y promociones" />
          </Form.Group>
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
            disabled={
              form.password !== form.confirmPassword && form.password !== ""
            }
          >
            Registrarme
          </button>
        </Form>
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
    name: "No aplica",
  },
  {
    id: 2,
    name: "Régimen Común",
  },
  {
    id: 3,
    name: "Régimen Simplificado",
  },
];
