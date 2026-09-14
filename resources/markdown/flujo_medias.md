flowchart TD
    A[Usuario] --> B[Selecciona imágenes en input file Livewire]

    B --> C[Subida de imágenes: store('categorias|productos', 'public')]
    C --> D[Crear registros en Media]
    D --> E[Asignar orden_visual incremental]
    D --> F[Asignar is_primary según primaryImageId]
    D --> G[Guardar atributos relacionados (si existen)]

    H[Usuario marca imagen como primaria] --> I[Actualizar todas las medias]
    I --> J[Seleccionada: is_primary = 1]
    I --> K[Otras: is_primary = 0]

    L[Usuario marca imágenes para eliminar] --> M[Eliminar archivos del storage]
    M --> N[Eliminar registros de Media]
    N --> O{Se eliminó la primaria?}
    O -- Sí --> P[Restaurar primaria original si existe]
    O -- No --> Q[Primera imagen por orden_visual se convierte en primaria]

    R[Drag & Drop Categorías] --> S[Actualizar orden_visual de categorías]
    T[Drag & Drop Imágenes Productos] --> U[Actualizar orden_visual de medias]
    U --> V[Primera imagen del array: is_primary = 1]
    U --> W[Actualizar las demás: is_primary = 0]
    U --> X[Refrescar colección loadMedias()]

    Y[Acciones completadas] --> Z[Dispatch Livewire alert & actualizaciones UI]

    C --> Z
    H --> Z
    L --> Z
    R --> Z
    X --> Z

    subgraph Subida & Media
        C --> D --> E --> F --> G
    end

    subgraph Primaria
        H --> I --> J & K
    end

    subgraph Eliminación
        L --> M --> N --> O --> P & Q
    end

    subgraph DragDrop
        R --> S
        T --> U --> V & W --> X
    end

    Z --> AA[Logger / Debug en caso de error]
