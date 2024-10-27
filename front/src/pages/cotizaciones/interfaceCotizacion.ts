export interface IHerramientasCotizadas {
  id: number;
  name: string;
  price: number;
  marca: string;
  puntuacion: number;
  code: string;
  cantidad: number;
  defaultState: {
    value: number;
    label: string;
  };
}
export interface ICotizaciones {
  herramientas: IHerramientasCotizadas[];
  opcionesHerramientas: { value: number; label: string }[];
  idUser: number;
}
