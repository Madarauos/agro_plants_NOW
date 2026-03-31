let currentPosition = 0
let itemsVisible = 3
let isAutoScrolling = true
let autoScrollInterval = null
let totalItems = 0

let touchStartX = 0
let touchEndX = 0
let isDragging = false

function updateItemsVisible() {
  const screenWidth = window.innerWidth
  if (screenWidth < 768) {
    itemsVisible = 1
  } else if (screenWidth < 1024) {
    itemsVisible = 2
  } else {
    itemsVisible = 3
  }
  updateCarouselLayout()
}

function updateCarouselLayout() {
  const carousel = document.getElementById("carouselContent")
  const cards = carousel.querySelectorAll(".product-card")

  if (!carousel || cards.length === 0) return

  totalItems = cards.length

  const cardWidth = 100 / itemsVisible
  const gap = itemsVisible === 1 ? 0 : 30

  cards.forEach((card, index) => {
    card.style.width = `calc(${cardWidth}% - ${gap}px)`
    card.style.marginRight = itemsVisible === 1 ? "0" : "15px"
    card.style.marginLeft = itemsVisible === 1 ? "0" : "15px"
  })

  const maxPosition = totalItems - itemsVisible
  if (currentPosition > maxPosition) {
    currentPosition = maxPosition
  }

  updateCarouselPosition()
}

function moveCarousel(direction) {
  const carousel = document.getElementById("carouselContent")
  const cards = carousel.querySelectorAll(".product-card")
  if (!carousel || cards.length === 0) return

  stopAutoScroll()

  currentPosition += direction

  const maxPosition = cards.length - itemsVisible

  if (currentPosition < 0) {
    currentPosition = maxPosition
  } else if (currentPosition > maxPosition) {
    currentPosition = 0
  }

  updateCarouselPosition()
  updateDots()

  setTimeout(() => {
    startAutoScroll()
  }, 3000)
}

function updateCarouselPosition() {
  const carousel = document.getElementById("carouselContent")
  if (!carousel) return

  const translateX = -(currentPosition * (100 / itemsVisible))
  carousel.style.transform = `translateX(${translateX}%)`
}

function goToSlide(index) {
  const carousel = document.getElementById("carouselContent")
  const cards = carousel.querySelectorAll(".product-card")
  if (!carousel || cards.length === 0) return

  stopAutoScroll()
  currentPosition = Math.max(0, Math.min(index, cards.length - itemsVisible))
  updateCarouselPosition()
  updateDots()

  setTimeout(() => {
    startAutoScroll()
  }, 3000)
}

function autoScroll() {
  if (!isAutoScrolling) return
  const carousel = document.getElementById("carouselContent")
  const cards = carousel.querySelectorAll(".product-card")
  if (!carousel || cards.length === 0) return

  currentPosition++
  const maxPosition = cards.length - itemsVisible
  if (currentPosition > maxPosition) {
    currentPosition = 0
  }

  updateCarouselPosition()
  updateDots()
}

function startAutoScroll() {
  if (autoScrollInterval) clearInterval(autoScrollInterval)
  isAutoScrolling = true
  autoScrollInterval = setInterval(autoScroll, 4000)
}

function stopAutoScroll() {
  isAutoScrolling = false
  if (autoScrollInterval) {
    clearInterval(autoScrollInterval)
    autoScrollInterval = null
  }
}

function handleTouchStart(e) {
  touchStartX = e.touches[0].clientX
  isDragging = true
  stopAutoScroll()
}

function handleTouchMove(e) {
  if (!isDragging) return
  touchEndX = e.touches[0].clientX
  if (Math.abs(touchStartX - touchEndX) > 10) {
    e.preventDefault()
  }
}

function handleTouchEnd() {
  if (!isDragging) return
  isDragging = false

  const swipeThreshold = 50
  const swipeDistance = touchStartX - touchEndX

  if (Math.abs(swipeDistance) > swipeThreshold) {
    if (swipeDistance > 0) {
      moveCarousel(1)
    } else {
      moveCarousel(-1)
    }
  } else {
    setTimeout(startAutoScroll, 3000)
  }
}

function createDots() {
  const carousel = document.getElementById("carouselContent")
  const cards = carousel.querySelectorAll(".product-card")
  const dotsContainer = document.getElementById("carouselDots")

  if (!dotsContainer || !carousel || cards.length === 0) return

  dotsContainer.innerHTML = ""

  if (window.innerWidth >= 1024) {
    dotsContainer.style.display = "none"
    return
  }

  dotsContainer.style.display = "flex"
  const totalDots = cards.length - itemsVisible + 1

  for (let i = 0; i < totalDots; i++) {
    const dot = document.createElement("button")
    dot.className = `carousel-dot ${i === currentPosition ? "active" : ""}`
    dot.setAttribute("aria-label", `Ir para slide ${i + 1}`)
    dot.addEventListener("click", () => {
      goToSlide(i)
    })
    dotsContainer.appendChild(dot)
  }
}

function updateDots() {
  const dots = document.querySelectorAll(".carousel-dot")
  dots.forEach((dot, index) => {
    dot.classList.toggle("active", index === currentPosition)
  })
}

document.addEventListener("DOMContentLoaded", () => {
  const carousel = document.getElementById("carouselContent")
  if (!carousel) return

  updateItemsVisible()

  carousel.addEventListener("touchstart", handleTouchStart, { passive: true })
  carousel.addEventListener("touchmove", handleTouchMove, { passive: false })
  carousel.addEventListener("touchend", handleTouchEnd, { passive: true })

  carousel.addEventListener("mouseenter", stopAutoScroll)
  carousel.addEventListener("mouseleave", startAutoScroll)

  window.addEventListener("resize", () => {
    updateItemsVisible()
    createDots()
  })

  createDots()

  startAutoScroll()
})

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
        })
      }
    })
  })
})
 document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                initializeMenuFix();
            }, 100);
        });

function initializeMenuFix() {
  const hamburgerMenu = document.querySelector(".jp_hamburger-menu");
  const sidebar = document.querySelector(".jp_sidebar");
  const overlay = document.querySelector(".jp_overlay");
  const body = document.body;

  if (!hamburgerMenu || !sidebar || !overlay) {
      console.log("Elementos do menu não encontrados, tentando novamente...");
      setTimeout(initializeMenuFix, 200);
      return;
  }

  console.log("Menu elements found, initializing...");

  hamburgerMenu.replaceWith(hamburgerMenu.cloneNode(true));
  const newHamburgerMenu = document.querySelector(".jp_hamburger-menu");

  newHamburgerMenu.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log("Hamburger clicked");
      toggleMenuFix();
  });

  overlay.addEventListener("click", function() {
      console.log("Overlay clicked");
      closeMenuFix();
  });

  const navLinks = sidebar.querySelectorAll(".jp_nav-links a");
  navLinks.forEach(function(link) {
      link.addEventListener("click", function() {
          closeMenuFix();
      });
  });

  document.addEventListener("keydown", function(event) {
      if (event.key === "Escape" && sidebar.classList.contains("active")) {
          closeMenuFix();
      }
  });

  window.addEventListener("resize", function() {
      if (window.innerWidth > 992 && sidebar.classList.contains("active")) {
          closeMenuFix();
      }
  });

  function toggleMenuFix() {
      const isActive = sidebar.classList.contains("active");
      console.log("Menu is active:", isActive);
      
      if (isActive) {
          closeMenuFix();
      } else {
          openMenuFix();
      }
  }

  function openMenuFix() {
      console.log("Opening menu");
      newHamburgerMenu.classList.add("active");
      sidebar.classList.add("active");
      overlay.classList.add("active");
      body.classList.add("menu-open");
      
      sidebar.offsetHeight;
  }

  function closeMenuFix() {
      console.log("Closing menu");
      newHamburgerMenu.classList.remove("active");
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
      body.classList.remove("menu-open");
  }
}