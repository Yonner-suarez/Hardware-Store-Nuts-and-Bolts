import React, { createContext, useContext, useState, useEffect } from "react";

interface IBag {
  id: number;
  name: string;
  price: string;
  marca: string;
  puntuacion: number;
  code: string;
  id_quote: number;
  cantidad: number;
}
// Crear un contexto
const BagContext = createContext<BagContextProps>({
  bag: [],
  setBag: (bag: IBag[]) => {},
});

// Hook para usar el contexto
export const useBag = () => {
  return useContext(BagContext);
};

// Proveedor del contexto
export const BagProvider = ({ children }: any) => {
  const [bag, setBag] = useState<IBag[]>(() => {
    // Inicializar el carrito desde Local Storage
    return JSON.parse(localStorage.getItem("bag") || "[]");
  });

  useEffect(() => {
    // Actualizar localStorage cuando cambie el carrito
    localStorage.setItem("bag", JSON.stringify(bag));
  }, [bag]);

  return (
    <BagContext.Provider value={{ bag, setBag }}>
      {children}
    </BagContext.Provider>
  );
};
