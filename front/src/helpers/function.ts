import { BASE_URL, GEO_URL, geo } from "./api";
import axios from "axios";

export const obtenerNombreCiudad = async (
  latitude: number,
  longitude: number
): Promise<string> => {
  try {
    const url = `${GEO_URL}${geo.GEO_URL}?format=json&lat=${latitude}&lon=${longitude}&zoom=10&addressdetails=1`;
    const response = await axios.get(url);
    const data = response.data;
    if (data && data.address) {
      const ciudad =
        data.address.city ||
        data.address.town ||
        data.address.village ||
        "Desconocida";
      return ciudad;
    }
    throw "Ciudad no encontrada";
  } catch (error) {
    throw "Algo salió mal";
  }
};
