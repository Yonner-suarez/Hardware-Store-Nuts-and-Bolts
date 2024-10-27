import { useEffect, useRef, useState } from "react";
import ModalRegisterLogin from "../../components/ModalLogin/ModalLogin";
import { Modal } from "bootstrap";
import TipoProductos from "../../components/TiposProductos/TipoProductos";
import CarruselHomePage from "../../components/CarruselHomePage/CarruselHomePage";

const Home: React.FC = () => {
  const modalRef = useRef<Modal | null>(null);

  useEffect(() => {
    const token = localStorage.getItem("token");

    if (!token) {
      const modalElement = document.getElementById("exampleModal");
      if (modalElement) {
        modalRef.current = new Modal(modalElement);
        modalRef.current.show();

        modalElement.addEventListener("hidden.bs.modal", () => {
          // Cleanup on modal close
          document.body.classList.remove("modal-open");
          const backdrop = document.querySelector(".modal-backdrop");
          if (backdrop) {
            backdrop.remove();
          }
        });
        // Limpiar el efecto cuando el componente se desmonte
        return () => {
          if (modalRef.current) {
            modalRef.current.hide(); // Asegúrate de que esto se llama
          }
          document.body.classList.remove("modal-open");
          const backdrop = document.querySelector(".modal-backdrop");
          if (backdrop) {
            backdrop.remove();
          }
        };
      }
    }
  }, []);
  return (
    <>
      <ModalRegisterLogin modalRef={modalRef} />
      <CarruselHomePage />
      <TipoProductos />
    </>
  );
};

export default Home;
