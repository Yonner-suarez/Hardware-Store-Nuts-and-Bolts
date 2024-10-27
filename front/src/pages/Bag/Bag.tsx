import { useContext, useEffect, useState } from "react";
import { useBag } from "../../helpers/BagContext";
import styles from "./Bag.module.css";
import numeral from "numeral";
import AddIcon from "../../../assets/ph--plus-fill.svg";
import RemoveIcon from "../../../assets/fluent--subtract-square-16-filled.svg";
import BagEmpty from "../../components/BagEmpty/BagEmpty";
import seguridad from "../../../assets/mingcute--user-security-fill.svg";
import satisfaccion from "../../../assets/vaadin--medal.svg";
import ayuda from "../../../assets/icon-park-solid--phone-two.svg";
import pseIcon from "../../../assets/pse-icon.png";
import Tool from "../Tools/Tools";
import axios from "axios";
import Swal from "sweetalert2";
import IconTrash from "../../../assets/f7--trash-circle-fill.svg";

const Bag: React.FC = () => {
  const { bag, setBag } = useBag();
  const [descuento, setDescuento] = useState(0);

  useEffect(() => {
    const obtenerDescuento = async () => {
      const descuento = await getDiscount();
      if (descuento) {
        setDescuento(descuento);
      }
    };
    obtenerDescuento();
  }, []);

  const SetterCantidad = async (
    id: number,
    accion: string,
    cantidad: number,
    id_quote: any = null
  ) => {
    const url =
      "http://localhost/Hardware-Store-Nuts-and-Bolts/pruebaphpapi/quotes/setCantidad";
    const token = localStorage.getItem("token");
    const headers = {
      Authorization: `Bearer ${token}`,
    };
    let aux = 0;
    if (accion === "add") {
      aux = cantidad += 1;
    } else {
      aux = cantidad -= 1;
    }
    const response = await axios.put(
      url,
      { idproducto: id, cantidad: aux, id_quote: id_quote },
      { headers }
    );
    console.log(response);
  };
  const calcularTotalSub = () => {
    const total = bag.reduce(
      (acc: number, item: any) => acc + parseFloat(item.price) * item.cantidad,
      0
    );
    return total;
  };

  const calcularDescuento = () => {
    const total = calcularTotalSub();
    let descuentoAplicado = 0;

    if (descuento > 0) {
      descuentoAplicado = total * descuento;
    }

    if (total > 100000) {
      const descuentoAdicional = total * 0.02;
      descuentoAplicado += descuentoAdicional;
    }

    return descuentoAplicado;
  };

  const handleCantidadProducto = async (
    accion: string,
    id: number,
    id_quote: any = null
  ) => {
    const url =
      "http://localhost/Hardware-Store-Nuts-and-Bolts/pruebaphpapi/quotes/TrashProduct";
    const token = localStorage.getItem("token");
    const headers = {
      Authorization: `Bearer ${token}`,
    };
    const response = await axios.put(
      url,
      {
        idproducto: id,
        id_quote: id_quote,
      },
      { headers }
    );
    console.log(response);
  };

  const handleTrashQuote = async (id_quote: any) => {
    Swal.fire({
      title: "¿Estás seguro de eliminar la cotización?",
      text: "No podrás revertir esto!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si, eliminar!",
    }).then(async (result) => {
      if (result.isConfirmed) {
        const url =
          "http://localhost/Hardware-Store-Nuts-and-Bolts/pruebaphpapi/quotes/TrashQuote";
        const token = localStorage.getItem("token");
        const headers = {
          Authorization: `Bearer ${token}`,
        };
        const response = await axios.put(
          url,
          {
            id_quote: id_quote,
          },
          { headers }
        );
        Swal.fire({
          title: "Eliminado!",
          text: response.data.message,
          icon: "success",
        });
      }
    });
  };

  const showAlert = (message: string) => {
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      },
    });

    Toast.fire({
      icon: "success",
      title: message,
    });
  };

  // Agrupar por id_quote
  const agrupadasPorCotizacion = bag.reduce((acc: any, item: any) => {
    if (!acc[item.id_quote]) {
      acc[item.id_quote] = []; // Si no existe, inicializa un array
    }
    acc[item.id_quote].push(item); // Agrega el elemento al array correspondiente
    return acc;
  }, {});

  // Convertir a un array para el mapeo
  const cotizacionesArray = Object.entries(agrupadasPorCotizacion).map(
    ([id_quote, items]) => ({
      id_quote,
      items,
    })
  );

  const getDiscount = async () => {
    const token = localStorage.getItem("token");
    const headers = {
      Authorization: `Bearer ${token}`,
    };
    try {
      const url = `http://localhost/Hardware-Store-Nuts-and-Bolts/pruebaphpapi/quotes/ToolsSaleDiscount`;
      const response = await axios.get(url, { headers });
      if (response.data.status == "200" && response.data.data > 0) {
        showAlert(
          `Estimado cliente por tu confianza en nosotros tienes: ${
            response.data.data * 100
          }% de descuento`
        );
        return response.data.data;
      }
      return null;
    } catch (error) {
      console.log(error);
      return null;
    }
  };

  return (
    <div className={styles.container}>
      <div className={styles.bagContent}>
        <div className={styles.bagItems}>
          {bag.length <= 0 ? (
            <BagEmpty />
          ) : (
            bag
              .filter(
                (item) =>
                  !cotizacionesArray.some((cot) =>
                    cot.items.some((i) => i.id === item.id)
                  )
              ) // Filtrar los productos que ya están en cotizaciones
              .map((item) => (
                <div key={item.id} className={styles.bagItem}>
                  <img
                    src={`../../../assets/tools/${item.name}.svg`}
                    alt={item.name}
                    style={{ width: "10%" }}
                  />
                  <div className={styles.itemInfo}>
                    <h4>{item.marca}</h4>
                    <h6 className={styles.itemName}>{item.name}</h6>
                    <p>Código: {item.code}</p>
                  </div>
                  <div>
                    <p className={styles.itemPrice}>
                      $ {numeral(item.price).format("0,0.00")}
                    </p>
                    <img
                      title="Aumentar unidades"
                      src={AddIcon}
                      style={{ width: "48px", cursor: "pointer" }}
                      alt="add"
                      onClick={() =>
                        SetterCantidad(item.id, "add", item.cantidad)
                      }
                    />
                    <span>{item.cantidad > 0 ? item.cantidad : 0}</span>
                    <img
                      title="Disminuir unidades"
                      src={RemoveIcon}
                      style={{ width: "48px", cursor: "pointer" }}
                      alt="sub"
                      onClick={() =>
                        SetterCantidad(item.id, "sub", item.cantidad)
                      }
                    />
                  </div>
                  <img
                    onClick={() => handleCantidadProducto("sub", item.id)}
                    src={IconTrash}
                    style={{ width: "48px", cursor: "pointer" }}
                    title="Eliminar Producto"
                    alt="trash"
                  />
                </div>
              ))
          )}

          {/* Mostrar cotizaciones */}
          {cotizacionesArray.length > 0 &&
            cotizacionesArray.map(({ id_quote, items }, index) => (
              <div key={index}>
                <hr />
                <div
                  style={{
                    display: "flex",
                    justifyContent: "space-between",
                    flexDirection: "row",
                  }}
                >
                  <h2>
                    {id_quote && id_quote !== "undefined" && id_quote !== ""
                      ? `Cotización # ${id_quote}`
                      : "Productos sin cotización"}
                  </h2>
                  <img
                    onClick={() => handleTrashQuote(id_quote)}
                    src={IconTrash}
                    style={{ width: "48px", cursor: "pointer" }}
                    title="Eliminar Cotización"
                    alt="trash"
                  />
                </div>
                {items.map((item) => (
                  <div key={item.id} className={styles.bagItem}>
                    <img
                      src={`../../../assets/tools/${item.name}.svg`}
                      alt={item.name}
                      style={{ width: "10%" }}
                    />
                    <div className={styles.itemInfo}>
                      <h4>{item.marca}</h4>
                      <h6 className={styles.itemName}>{item.name}</h6>
                      <p>Código: {item.code}</p>
                    </div>
                    <div>
                      <p className={styles.itemPrice}>
                        $ {numeral(item.price).format("0,0.00")}
                      </p>
                      <img
                        title="Aumentar unidades"
                        src={AddIcon}
                        style={{ width: "48px", cursor: "pointer" }}
                        alt="add"
                        onClick={() =>
                          SetterCantidad(
                            item.id,
                            "add",
                            item.cantidad,
                            id_quote
                          )
                        }
                      />
                      <span>{item.cantidad}</span>
                      <img
                        title="Disminuir unidades"
                        src={RemoveIcon}
                        style={{ width: "48px", cursor: "pointer" }}
                        alt="sub"
                        onClick={() =>
                          SetterCantidad(
                            item.id,
                            "sub",
                            item.cantidad,
                            id_quote
                          )
                        }
                      />
                    </div>
                    <img
                      onClick={() =>
                        handleCantidadProducto("sub", item.id, id_quote)
                      }
                      src={IconTrash}
                      style={{ width: "48px", cursor: "pointer" }}
                      title="Eliminar Producto"
                      alt="trash"
                    />
                  </div>
                ))}
              </div>
            ))}

          <hr style={{ border: "2px solid black" }} />
          <h5>Productos similares</h5>
          <Tool filtro={false} />
        </div>

        <div className={styles.summary}>
          <h6 className={styles.summaryTitle} style={{ textAlign: "center" }}>
            Resumen de compra
          </h6>
          <div>
            <div className={styles.summaryItem}>
              <h6 className={styles.summaryTitle}>Subtotal</h6>
              <p className={styles.summaryItemPrice}>
                $ {numeral(calcularTotalSub()).format("0,0.00")}
              </p>
            </div>

            <hr style={{ border: "2px solid black" }} />
            <div style={{ display: "flex", justifyContent: "center" }}>
              <span
                style={{
                  color: "black",
                  fontFamily: "normal",
                  textAlign: "start",
                }}
              >
                Si la compra supera los $100.000 tienes un 2% de descuento
                adicional
              </span>
            </div>

            <div className={styles.summaryItem}>
              <h6 className={styles.summaryTitle}>Descuento</h6>
              <p className={styles.summaryItemPrice}>
                $ {numeral(calcularDescuento()).format("0,0.00")}
              </p>
            </div>
            <div className={styles.summaryItem}>
              <h6 className={styles.summaryTitle}>Total</h6>
              <p className={styles.summaryItemPrice}>
                $ {numeral(calcularTotalSub()).format("0,0.00")}
              </p>
            </div>
            <p style={{ color: "black", fontFamily: "normal" }}>
              El coste de envío no está incluido en el total
            </p>

            <hr style={{ border: "2px solid black" }} />
            <div
              style={{
                display: "flex",
                alignItems: "center",
                justifyContent: "space-around",
              }}
            >
              <p className={styles.summaryTitle}>Pagar con PSE</p>
              <img
                src={pseIcon}
                alt="pse icon"
                style={{
                  width: "15%",
                  marginBottom: "2%",
                  cursor: "pointer",
                  title: "Pagar con PSE",
                }}
              />
            </div>

            <div className={styles.infoBox}>
              <div className={styles.infoItem}>
                <img
                  src={seguridad}
                  alt="icono seguridad"
                  className={styles.infoIcon}
                />
                <div className={styles.infoContent}>
                  <h4>Compra segura</h4>
                  <p>
                    Tus datos personales se mantienen bajo estricta
                    confidencialidad y están protegidos.
                  </p>
                </div>
              </div>

              <div className={styles.infoItem}>
                <img
                  src={satisfaccion}
                  alt="icono satisfacción"
                  className={styles.infoIcon}
                />
                <div className={styles.infoContent}>
                  <h4>Satisfacción Garantizada</h4>
                  <p>
                    Puedes devolver tu compra en un plazo máximo de 30 días, el
                    producto debe estar en perfecto estado: sin uso, tener todos
                    sus accesorios, manuales y embalaje original. Si tienes
                    dudas, comunícate a nuestra línea de atención al cliente
                    desde Bogotá 3077115 o a la línea nacional 320 889 9933.
                  </p>
                </div>
              </div>

              <div className={styles.infoItem}>
                <img
                  src={ayuda}
                  alt="icono ayuda"
                  className={styles.infoIcon}
                />
                <div className={styles.infoContent}>
                  <h4>¿Necesitas ayuda?</h4>
                  <p>
                    Si necesitas ayuda para completar tu compra llámanos al 320
                    889 9933
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Bag;
