import { useEffect, useRef, useState } from "react";
import ModalRegisterLogin from "../../components/ModalLogin/ModalLogin";
import { Modal } from "bootstrap";
import TipoProductos from "../../components/TiposProductos/TipoProductos";
import CarruselHomePage from "../../components/CarruselHomePage/CarruselHomePage";

const Home: React.FC = () => {
  const modalRef = useRef<Modal | null>(null);

  useEffect(() => {
    const token = localStorage.getItem("token");
    const modalElement = document.getElementById("exampleModal");

    if (!token) {
      if (modalElement) {
        modalRef.current = new Modal(modalElement);
        modalRef.current.show();
      }
    }
    const handleModalHidden = () => {
      // Cleanup on modal close
      document.body.classList.remove("modal-open");
      document.body.style.overflow = "auto"; // Asegúrate de restablecer el scroll
      const backdrop = document.querySelector(".modal-backdrop");
      if (backdrop) {
        backdrop.remove();
      }
    };

    modalElement?.addEventListener("hidden.bs.modal", handleModalHidden);

    // Limpiar el efecto cuando el componente se desmonte
    return () => {
      if (modalRef.current) {
        modalRef.current.hide();
      }
      handleModalHidden(); // Asegúrate de limpiar estilos al desmontar
      modalElement?.removeEventListener("hidden.bs.modal", handleModalHidden);
    };
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
