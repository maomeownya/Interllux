const luzonData = {
    "regions": [
      {
        "name": "Ilocos Region",
        "provinces": {
          "Ilocos Norte": ["Laoag"],
          "Ilocos Sur": ["Vigan", "Candon"],
          "La Union": ["San Fernando"],
          "Pangasinan": ["Alaminos", "Dagupan", "San Carlos", "Urdaneta"]
        }
      },
      {
        "name": "Cagayan Valley",
        "provinces": {
          "Batanes": [],
          "Cagayan": ["Tuguegarao"],
          "Isabela": ["Cauayan", "Santiago", "Ilagan"],
          "Nueva Vizcaya": [],
          "Quirino": []
        }
      },
      {
        "name": "Central Luzon",
        "provinces": {
          "Aurora": [],
          "Bataan": ["Balanga"],
          "Bulacan": ["Malolos", "Meycauayan", "San Jose del Monte"],
          "Nueva Ecija": ["Cabanatuan", "Gapan","Munoz", "San Jose"],
          "Pampanga": ["Angeles","Mabalacat", "San Fernando"],
          "Tarlac": ["Tarlac"],
          "Zambales": ["Olongapo"]
        }
      },
      {
        "name": "CALABARZON",
        "provinces": {
          "Batangas": ["Batangas City", "Lipa", "Tanauan"],
          "Cavite": ["Bacoor", "Cavite City", "Tagaytay", "Trece Martires"],
          "Laguna": ["Biñan", "Cabuyao", "Calamba", "San Pablo", "San Pedro", "Santa Rosa"],
          "Quezon": ["Lucena","Tayabas"],
          "Rizal": ["Antipolo"]
        }
      },
      {
        "name": "MIMAROPA",
        "provinces": {
          "Marinduque": [],
          "Occidental Mindoro": ["San Jose"],
          "Oriental Mindoro": ["Calapan"],
          "Palawan": ["Puerto Princesa"],
          "Romblon": []
        }
      },
      {
        "name": "Bicol Region",
        "provinces": {
          "Albay": ["Legazpi", "Ligao","Tabaco"],
          "Camarines Norte": [],
          "Camarines Sur": ["Naga", "Iriga"],
          "Catanduanes": [],
          "Masbate": ["Masbate City"],
          "Sorsogon": ["Sorsogon City"]
        }
      },
      {
        "name": "Cordillera Administrative Region",
        "provinces": {
          "Abra": [],
          "Apayao": [],
          "Benguet": ["Baguio"],
          "Ifugao": [],
          "Kalinga": ["Tabuk"],
          "Mountain Province": []
        }
      },
      {
        "name": "National Capital Region",
        "provinces": {
          "Metro Manila": ["Caloocan", "Las Piñas", "Makati", "Malabon", "Mandaluyong", "Manila", "Marikina", "Muntinlupa", "Navotas", "Parañaque", "Pasay", "Pasig", "Quezon City", "San Juan", "Taguig", "Valenzuela"]
        }
      }
    ]
  };
  window.onload = function() {
    const regionSelect = document.getElementById("region");
    const provinceSelect = document.getElementById("province");
  
    // Populate regions on load
    luzonData.regions.forEach(region => {
      const option = document.createElement("option");
      option.value = region.name;
      option.text = region.name;
      regionSelect.add(option);
    });
    //This is a property of PLSP-CCST BSIT-3B SY 2024-2025
    // Attach event listeners
    regionSelect.addEventListener("change", populateProvinceDropdown);
    provinceSelect.addEventListener("change", populateCityDropdown);
  };
  
  function populateProvinceDropdown() {
    const regionSelect = document.getElementById("region");
    const provinceSelect = document.getElementById("province");
    const citySelect = document.getElementById("city");
  
    const selectedRegion = regionSelect.value;
    const regionData = luzonData.regions.find(region => region.name === selectedRegion);
  
    // Clear dropdowns
    provinceSelect.innerHTML = '<option value="">Select Province...</option>';
    citySelect.innerHTML = '<option value="">Select City...</option>';
  
    if (regionData && regionData.provinces) {
      Object.keys(regionData.provinces).forEach(province => {
        const option = document.createElement("option");
        option.value = province;
        option.text = province;
        provinceSelect.add(option);
      });
    }
  }
  
  function populateCityDropdown() {
    const regionSelect = document.getElementById("region");
    const provinceSelect = document.getElementById("province");
    const citySelect = document.getElementById("city");
  
    const selectedRegion = regionSelect.value;
    const selectedProvince = provinceSelect.value;
  
    const regionData = luzonData.regions.find(region => region.name === selectedRegion);
    const cities = regionData && regionData.provinces[selectedProvince] ? regionData.provinces[selectedProvince] : [];
  
    // Clear city dropdown
    citySelect.innerHTML = '<option value="">Select City...</option>';
  
    if (cities.length > 0) {
      cities.forEach(city => {
        const option = document.createElement("option");
        option.value = city;
        option.text = city;
        citySelect.add(option);
      });
    }
}
  