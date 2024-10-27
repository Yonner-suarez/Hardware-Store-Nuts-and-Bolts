import Select from "react-select";
import { useEffect, useState } from "react";
import { ICotizaciones, IHerramientasCotizadas } from "./interfaceCotizacion";
import styles from "./Cotizaciones.module.css";
import Swal from "sweetalert2";
import AddIcon from "../../../assets/ph--plus-fill.svg";
import RemoveIcon from "../../../assets/fluent--subtract-square-16-filled.svg";
import IconCotizador from "../../../assets/pajamas--review-list.svg";
import Loader from "../../components/Loader/Loader";
import IconTrash from "../../../assets/f7--trash-circle-fill.svg";
import numeral from "numeral";
import { useNavigate } from "react-router-dom";
import { jwtDecode } from "jwt-decode";
import axios from "axios";
import { useBag } from "../../helpers/BagContext";
const Cotizaciones: React.FC = () => {
  const navigate = useNavigate();

  const { bag, setBag } = useBag();

  const [totalCotizacion, setTotalCotizacion] = useState(0);
  const [showLoading, setShowLoading] = useState({ display: "none" });
  const [cotizaciones, setCotizaciones] = useState<ICotizaciones>({
    herramientas: [],
    opcionesHerramientas: [{ value: -1, label: "Seleccione una opción" }],
    idUser: 0,
  });
  const [cantidadProducto, setCantidadProducto] = useState(0);

  useEffect(() => {
    const fetchOptions = async () => {
      const token = localStorage.getItem("token");

      if (!token) {
        Swal.fire(
          "Alerta",
          "Debes iniciar sesión para generar una cotización",
          "warning"
        );
        navigate("/HardwareStore/register/user");
        return; // Detener la ejecución si no hay token
      }

      const user = jwtDecode(token);
      setCotizaciones((prev) => ({
        ...prev,
        idUser: user.aud,
      }));

      const herramienta = cotizaciones.herramientas.find((h) => {
        const existeEnOpciones = cotizaciones.opcionesHerramientas.some(
          (opcion) => opcion.value === h.id
        );

        return h.defaultState.value !== -1 && existeEnOpciones;
      });

      if (herramienta) {
        setCotizaciones((prev) => {
          return {
            ...prev,
            opcionesHerramientas: prev.opcionesHerramientas.filter(
              (h) => h.value !== herramienta.id
            ),
          };
        });
      } else {
        const options = await getOptionsTools();
        setCotizaciones((prev) => ({
          ...prev,
          opcionesHerramientas: setOptionsSelect("defaultState", options),
        }));
      }
    };

    fetchOptions();
  }, [cotizaciones.herramientas]);

  const getHerramientas = async () => {
    const url =
      "http://localhost/Hardware-Store-Nuts-and-Bolts/pruebaphpapi/tool/tools";
    const response = await axios.get(url);
    return response.data.data;
  };

  const handleChange = (e: any) => {
    if (e.target === undefined) {
      const herramientaParaAgregar: IHerramientasCotizadas = {
        cantidad: 1,
        price: 0,
        id: e.value,
        defaultState: { value: e.value, label: e.label },
      };
      setCotizaciones({
        ...cotizaciones,
        herramientas: [
          ...cotizaciones.herramientas,
          herramientaParaAgregar,
        ] as IHerramientasCotizadas[],
      });
    }
  };
  const setOptionsSelect = (name: string, obj: any) => {
    let defaultOption: any = [];
    let estadoTmp = [
      ...defaultOption,
      ...obj.map((client: any) => ({
        name,
        value: client.value,
        label: client.label,
      })),
    ];
    return estadoTmp;
  };
  const SetterCantidad = (idHerramienta: number, type: string) => {
    const herramientaEncontrada = cotizaciones.herramientas.find(
      (h) => h.id === idHerramienta
    );
    if (type === "add") {
      if (herramientaEncontrada) {
        if (herramientaEncontrada && herramientaEncontrada.cantidad >= 15)
          return Swal.fire(
            "Alerta",
            "La cantidad no puede ser mayor a 15. Contactate con el administrador para comprar cantidades superiores",
            "warning"
          );

        herramientaEncontrada.cantidad += 1;
        setCotizaciones({
          ...cotizaciones,
          herramientas: cotizaciones.herramientas,
        });
      }
    } else {
      if (herramientaEncontrada && herramientaEncontrada.cantidad <= 0)
        return handleCantidadProducto("sub");

      if (herramientaEncontrada) {
        herramientaEncontrada.cantidad -= 1;
        setCotizaciones({
          ...cotizaciones,
          herramientas: cotizaciones.herramientas,
        });
      }
    }
  };
  const showCantidad = (idHerramienta: number) => {
    let herramienta = cotizaciones.herramientas.find((h) => {
      return h.defaultState.value === idHerramienta;
    });
    return herramienta?.cantidad;
  };

  const handleCantidadProducto = (type: string, idHerramienta: number = -1) => {
    if (type === "add") {
      if (cantidadProducto >= cotizaciones.opcionesHerramientas.length)
        return Swal.fire(
          "Alerta",
          "No se puede agregar más productos",
          "warning"
        );
      setCantidadProducto(cantidadProducto + 1);
    } else {
      if (idHerramienta !== -1) {
        const herramientaEncontrada = cotizaciones.herramientas.find(
          (h) => h.id === idHerramienta
        );
        if (herramientaEncontrada) {
          setCantidadProducto(cantidadProducto - 1);
          setCotizaciones({
            ...cotizaciones,
            herramientas: cotizaciones.herramientas.filter(
              (h) => h.id !== idHerramienta
            ),
          });
        }
      }
    }
  };

  const handleGenerateCotization = async () => {
    if (cotizaciones.herramientas.length === 0)
      return Swal.fire(
        "Alerta",
        "Debes seleccionar al menos una herramienta",
        "warning"
      );

    try {
      setShowLoading({ display: "block" });

      const token = localStorage.getItem("token");
      const headers = {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      };
      const url =
        "http://localhost/Hardware-Store-Nuts-and-Bolts/pruebaphpapi/quotes/quote";

      let tools = await getHerramientas();

      const parameters = {
        idusuario: cotizaciones.idUser,
        productos: cotizaciones.herramientas.map((h) => {
          return {
            idproducto: h.id,
            cantidad: h.cantidad,
            total: tools.find((t: any) => t.id === h.id)?.price,
          };
        }),
      };

      const response = await axios.post(url, parameters, { headers });

      if (response.data.status === "200") {
        const data = response.data.data;
        setTotalCotizacion(data.total);

        const herramientas = cotizaciones.herramientas.map((h) => ({
          ...tools.find((t: any) => t.id === h.id),
          id_quote: data.id_quote,
          cantidad: h.cantidad,
        }));
        setBag([...bag, ...herramientas]);
        Swal.fire("Exito", data.message, "success");
        setShowLoading({ display: "none" });
      } else {
        throw new Error(response.data.error);
      }
    } catch (error) {
      console.log(error);
      Swal.fire("Alerta", "Error al generar la cotización", "warning");
      setShowLoading({ display: "none" });
    }
  };

  const getOptionsTools = async () => {
    const url =
      "http://localhost/Hardware-Store-Nuts-and-Bolts/pruebaphpapi/tool/toolsoptions";

    try {
      const response = await axios.get(url);
      const data = response.data.data;
      return data;
    } catch (error) {
      Swal.fire("Alerta", "Error al obtener las herramientas", "warning");
      console.log(error);
      return [];
    }
  };
  return (
    <>
      <Loader estilo={showLoading} />
      <div className={styles.d_container_cotizacion}>
        <div>
          <h6
            style={{
              fontSize: "20px",
              fontWeight: "bold",
              color: "black",
              marginBottom: "5%",
            }}
          >
            Aquí puedes generar tu cotización
            <br />
            totalmente gratis y facil{" "}
            <img
              src={IconCotizador}
              alt="iconoCotizador"
              style={{ width: "20px" }}
            />
          </h6>
          <div className={styles.d_container_nombre_cotizacion}>
            <input
              type="text"
              placeholder="Ingresa el nombre de tu empresa"
              className={styles.i_nombre_cotizacion}
            />
            <div
              style={{
                display: "flex",
                flexDirection: "row",
                padding: "3px",
                width: "40%",
                justifyContent: "flex-end",
                justifyItems: "center",
                alignContent: "flex-end",
              }}
            >
              <img
                title="Agregar Producto"
                onClick={() => handleCantidadProducto("add")}
                src={AddIcon}
                style={{ width: "50px", cursor: "pointer" }}
              />

              <button
                title="Generar cotización"
                type="button"
                className="btn btn-primary"
                style={{
                  backgroundColor: "#004876",
                  color: "#fff",
                  border: "none",
                }}
                onClick={handleGenerateCotization}
              >
                Generar Cotizacion
              </button>
            </div>
          </div>

          {
            // Crear un array temporal con la longitud de cantidadProducto y mapearlo
            [...Array(cantidadProducto)].map((_, i) => (
              <div className={styles.d_container_herramienta}>
                <Select
                  id=""
                  name="defaultState"
                  placeholder="Seleccione una opción"
                  value={cotizaciones.herramientas[i]?.defaultState}
                  onChange={handleChange}
                  options={cotizaciones.opcionesHerramientas}
                  className={styles.select_herramienta}
                />
                <img
                  title="Aumentar unidades"
                  src={AddIcon}
                  style={{ width: "48px", cursor: "pointer" }}
                  alt="add"
                  onClick={() =>
                    SetterCantidad(cotizaciones.herramientas[i]?.id, "add")
                  }
                />
                <img
                  title="Disminuir unidades"
                  src={RemoveIcon}
                  style={{ width: "48px", cursor: "pointer" }}
                  alt="sub"
                  onClick={() =>
                    SetterCantidad(cotizaciones.herramientas[i]?.id, "sub")
                  }
                />
                <label className={styles.input_cantidad}>
                  Cantidad {showCantidad(cotizaciones.herramientas[i]?.id)}
                </label>
                <img
                  onClick={() =>
                    handleCantidadProducto(
                      "sub",
                      cotizaciones.herramientas[i]?.id
                    )
                  }
                  src={IconTrash}
                  style={{ width: "48px", cursor: "pointer" }}
                  title="Eliminar Producto"
                  alt="trash"
                />
                <label>
                  {cotizaciones.herramientas[i]?.price > 0
                    ? numeral(cotizaciones.herramientas[i]?.price).format(
                        "0,0.00"
                      )
                    : ""}
                </label>
              </div>
            ))
          }
        </div>
        <div
          style={{
            display: "flex",
            justifyContent: "space-around",
          }}
        >
          {totalCotizacion > 0 ? (
            <>
              <h6
                style={{ fontSize: "20px", fontWeight: "bold", color: "black" }}
              >
                Total de la cotización:
              </h6>
              <label
                style={{ fontSize: "20px", fontWeight: "bold", color: "black" }}
              >
                COP {numeral(totalCotizacion).format("0,0.00")}
              </label>
            </>
          ) : (
            ""
          )}
        </div>
      </div>
    </>
  );
};

export default Cotizaciones;
