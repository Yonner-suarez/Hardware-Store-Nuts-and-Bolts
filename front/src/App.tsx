import "./App.css";
import { Route, Routes } from "react-router-dom";
import { useState, useEffect } from "react";
import Navbar from "./components/NavBar/Navbar";
import Footer from "./components/Footer/Footer";
import Cotizaciones from "./pages/cotizaciones/Cotizaciones";
import Loader from "./components/Loader/Loader";
import { getTokenFromLocalStorage } from "./helpers/function";
import Home from "./pages/Home/Home";
import ModalRegister from "./components/ModalRegister/ModalRegister";
import Tools from "./pages/Tools/Tools";

function App() {
  const [showLoading, setShowLoading] = useState({ display: "none" });

  async function getToken() {
    const token = await getTokenFromLocalStorage();
    setShowLoading({ display: "none" });
  }

  useEffect(() => {
    setShowLoading({ display: "block" });
    getToken();
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
          path="/HardwareStore/products/construccion"
          element={<Tools />}
        />
        <Route
          path="/HardwareStore/products/latoneriaypintura"
          element={<Tools />}
        />
        <Route
          path="/HardwareStore/products/ornamentacion"
          element={<Tools />}
        />
      </Routes>

      <Footer />
    </>
  );
}

export default App;
