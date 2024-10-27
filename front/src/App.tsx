import "./App.css";
import { Route, Routes } from "react-router-dom";
import { useState, useEffect } from "react";
import Navbar from "./components/NavBar/Navbar";
import Footer from "./components/Footer/Footer";
import Cotizaciones from "./pages/cotizaciones/Cotizaciones";
import Loader from "./components/Loader/Loader";
import Home from "./pages/Home/Home";
import ModalRegister from "./components/ModalRegister/ModalRegister";
import Tools from "./pages/Tools/Tools";
import Bag from "./pages/Bag/Bag";
import Profile from "./pages/Profile/Profile";
import { useBag } from "./helpers/BagContext";
import { traerBag } from "./helpers/function";
function App() {
  const { bag, setBag } = useBag();
  const [showLoading, setShowLoading] = useState({ display: "none" });

  const ejecutarFunciones = async () => {
    const token = localStorage.getItem("token");

    if (token) {
      // Verificar si el token ha expirado
      const tokenData = JSON.parse(atob(token.split(".")[1]));
      const expirationTime = tokenData.exp * 1000;

      if (Date.now() >= expirationTime) {
        localStorage.removeItem("token");
      }
    }

    const aux = async () => {
      const herramientas = await traerBag();
      setBag(herramientas);
    };

    aux();
  };
  useEffect(() => {
    ejecutarFunciones();

    const intervalo = setInterval(ejecutarFunciones, 5000);

    // Limpia el intervalo cuando el componente se desmonte
    return () => clearInterval(intervalo);

    //setShowLoading({ display: "block" });
  }, []);

  return (
    <>
      <Loader
        estilo={showLoading}
        show={showLoading.display === "block" ? true : false}
      ></Loader>
      <Navbar />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route
          path="/HardawareStore/cotizaciones/generar"
          element={<Cotizaciones />}
        />
        <Route
          path="/HardwareStore/register/user"
          element={<ModalRegister />}
        />
        <Route
          path="/HardwareStore/products"
          element={<Tools filtro={true} />}
        />
        <Route path="/HardwareStore/user/profile" element={<Profile />} />
        <Route path="/HardwareStore/user/bag" element={<Bag />} />
      </Routes>

      <Footer />
    </>
  );
}

export default App;
