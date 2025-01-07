const containers = document.querySelector(".containers");
const containercarrossel = containers.querySelector(".container-carrossel");
const carrossel = containers.querySelector(".carrossel");
const carrosselItems = carrossel.querySelectorAll(".carrossel-item");
const contentText = document.getElementById("content-text");

// Function to update the content text based on which item is in the center
const updateDynamicContent = (itemId) => {
  let content = "";
  switch (itemId) {
    case "1":
      content = "User Information";
      break;
    case "2":
      content = "Nutritionist Information";
      break;
    case "3":
      content = "Fitness Class Member";
      break;
    case "4":
      content = "Dietary Consultation Request";
      break;
    default:
      content = "Welcome to Huan Fitness";
  }
  contentText.innerText = content;
};


const getCenteredItem = (rotationDegree) => {
  const length = carrosselItems.length;
  const degreesPerItem = 360 / length;
  
  const normalizedRotation = (rotationDegree % 360 + 360) % 360;
  
  const centeredIndex = Math.round(normalizedRotation / degreesPerItem) % length;
  
  const centeredItem = carrosselItems[centeredIndex];
  return centeredItem.getAttribute("data-id");
};

// Modify the update function to track rotation and update content
const update = () => {
  lastMoveTo = lerp(moveTo, lastMoveTo, 0.05);
  carrossel.style.setProperty("--rotatey", lastMoveTo + "deg");

  // Detect which item is centered based on rotation
  const centeredItemId = getCenteredItem(lastMoveTo);
  updateDynamicContent(centeredItemId);

  requestAnimationFrame(update);
};

const initEvents = () => {
  // Mouse and touch events for carousel interaction
  carrossel.addEventListener("mousedown", () => {
    isMouseDown = true;
    carrossel.style.cursor = "grabbing";
  });
  carrossel.addEventListener("mouseup", () => {
    isMouseDown = false;
    carrossel.style.cursor = "grab";
  });
  containers.addEventListener("mouseleave", () => (isMouseDown = false));
  
  carrossel.addEventListener("mousemove", (e) => isMouseDown && getPosX(e.clientX));

  carrossel.addEventListener("touchstart", () => {
    isMouseDown = true;
    carrossel.style.cursor = "grabbing";
  });
  carrossel.addEventListener("touchend", () => {
    isMouseDown = false;
    carrossel.style.cursor = "grab";
  });
  containers.addEventListener("touchmove", (e) => isMouseDown && getPosX(e.touches[0].clientX));

  window.addEventListener("resize", createcarrossel);

  update();
  createcarrossel();
};

initEvents();
