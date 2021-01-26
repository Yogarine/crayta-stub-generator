--- @class NewScript : Script<Entity>
local NewScript = {}

--- Script properties are defined here.
NewScript.Properties = {
	-- Example property
	--{ name = "health", type = "number", tooltip = "Current health", default = 100 },
}

--- This function is called on the server when this entity is created.
function NewScript:Init()
end

function NewScript:OnButtonPressed(buttonName)
end

return NewScript
