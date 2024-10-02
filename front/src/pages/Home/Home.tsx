import { useEffect, useRef } from "react";
import ModalRegisterLogin from "../../components/ModalLogin/ModalLogin";
import { Modal } from "bootstrap";

const Home: React.FC = () => {
  const modalRef = useRef<Modal | null>(null);

  useEffect(() => {
    const modalElement = document.getElementById("exampleModal");
    if (modalElement) {
      modalRef.current = new Modal(modalElement);
      modalRef.current.show();

      // Agregar un event listener para cuando el modal se oculte
      modalElement.addEventListener("hidden.bs.modal", () => {
        document.body.classList.remove("modal-open");
        const backdrop = document.querySelector(".modal-backdrop");
        if (backdrop) {
          backdrop.remove();
        }
      });
    }

    // Limpieza al desmontar el componente
    return () => {
      if (modalRef.current) {
        modalRef.current.dispose();
      }
    };
  }, []);
  return (
    <>
      <ModalRegisterLogin />
      <div>
        <h1>Homepage</h1>
      </div>
    </>
  );
};

export default Home;
