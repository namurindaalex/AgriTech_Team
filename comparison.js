class Crop {
    constructor(name, optimalTemperature, optimalHumidity, optimalSoilMoisture) {
        this.name = name;
        this.optimalTemperature = optimalTemperature; // {min: value, max: value}
        this.optimalHumidity = optimalHumidity;       // {min: value, max: value}
        this.optimalSoilMoisture = optimalSoilMoisture; // {min: value, max: value}
    }

    isSuitable(currentTemp, currentHumidity, currentMoisture) {
        return (currentTemp >= this.optimalTemperature.min && currentTemp <= this.optimalTemperature.max) &&
               (currentHumidity >= this.optimalHumidity.min && currentHumidity <= this.optimalHumidity.max) &&
               (currentMoisture >= this.optimalSoilMoisture.min && currentMoisture <= this.optimalSoilMoisture.max);
    }
}

// Create a list of crops with their conditions
const crops = [
    new Crop("Corn", {min: 20, max: 30}, {min: 60, max: 80}, {min: 20, max: 30}),
    new Crop("Wheat", {min: 10, max: 25}, {min: 50, max: 70}, {min: 10, max: 20}),
    new Crop("Rice", {min: 20, max: 30}, {min: 70, max: 90}, {min: 25, max: 35}),
    new Crop("Coffee", {min: 15, max: 24}, {min: 60, max: 70}, {min: 15, max: 25}),
    new Crop("Tomatoes", {min: 20, max: 30}, {min: 50, max: 70}, {min: 10, max: 20}),
    new Crop("Potatoes", {min: 15, max: 20}, {min: 60, max: 80}, {min: 15, max: 25}),
    new Crop("Lettuce", {min: 15, max: 20}, {min: 70, max: 80}, {min: 20, max: 30}),
    new Crop("Cucumbers", {min: 18, max: 24}, {min: 60, max: 80}, {min: 15, max: 30}),
];

// Example current conditions
const currentTemp = 22.0;
const currentHumidity = 65.0;
const currentMoisture = 25.0;

console.log("Crops suitable for current conditions:");
crops.forEach(crop => {
    if (crop.isSuitable(currentTemp, currentHumidity, currentMoisture)) {
        console.log(crop.name);
    }
});
