import "./App.css";
import { Route, Routes } from "react-router-dom";
import { useState, useEffect } from "react";
import Navbar from "./components/NavBar/Navbar";
import Footer from "./components/Footer/Footer";
import Cotizaciones from "./pages/Cotizaciones";
import Loader from "./components/Loader/Loader";
import { getTokenFromLocalStorage } from "./helpers/function";
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
        <Route>
          <Route
            path="/HardawareStore/cotizaciones/generar"
            element={<Cotizaciones />}
          />
        </Route>
      </Routes>
      <Footer />
    </>
  );
}

export default App;
